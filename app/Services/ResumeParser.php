<?php

namespace App\Services;

use App\Support\Url;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class ResumeParser
{


    public function parse(UploadedFile $file): array
    {
        // Livewire's TemporaryUploadedFile may live on a remote disk (e.g. S3),
        // in which case getRealPath() isn't a real filesystem path. get() reads
        // through the configured storage disk instead, working for any disk.
        $contents =  file_get_contents($file->getRealPath());

        $pdf = base64_encode($contents);
        \Log::info('Parsing resume');

        $res =  $this->parseDocument($pdf);
        \Log::info($res);
        return $res;
    }

    private function parseDocument(string $pdf): array
    {
        $url =  "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent";
        $headers = [
            'x-goog-api-key' => config('services.gemini.key'),
            'Content-Type' => 'application/json',
        ];

        $today = now()->format('F j, Y');

        $prompt = [
            'text' => <<<PROMPT
You are an expert resume/CV parser for a recruiting team. Extract candidate
information from the attached resume and return ONLY valid JSON matching the
provided schema.

Today's date is {$today}. Use it to resolve any "Present" / "Current" / open-ended
date ranges when calculating experience.

{
    "first_name": null,
    "last_name": null,
    "email": null,
    "phone": null,
    "location": null,
    "highest_qualification": null,
    "college": null,
    "experience_years": null,
    "current_company": null,
    "current_designation": null,
    "skills": null,
    "linkedin_url": null,
    "portfolio_url": null
}

Field-by-field guidance:
- first_name / last_name: split the candidate's full name from the header.
- email / phone / location: usually in the header, often prefixed with icons
  (a pin, phone, or envelope symbol) — extract the text value after the icon,
  not the icon itself.
- highest_qualification: the single highest level of education (PhD > Master's
  > Bachelor's > Diploma > Higher/Senior Secondary > Secondary). Use the exact
  degree name as written (e.g. "Bachelor of Computer Applications (BCA)").
- college: the institution/university name tied to that highest qualification.
  Only extract it if it is explicitly written in the resume text — never guess
  or infer an institution name that isn't printed.
- experience_years: total professional work experience (full-time roles AND
  internships combined), as a decimal number of years (e.g. 1.5). Compute it
  by summing the duration of every listed role. For a role with no end date,
  or marked "Present"/"Current", use today's date ({$today}) as the end date.
  If the candidate has no work experience or internships listed, return 0 —
  do not return null for this field.
- current_company / current_designation: the company and title of the
  candidate's most recent role. If that role is explicitly ongoing
  ("Present"/"Current"), it is their current position. If every listed role
  has already ended (e.g. a completed internship, or a fresh graduate with no
  open role), still return the most recent past company and designation here
  — only return null if the resume lists no work experience at all.
- skills: merge any sections labelled "Skills", "Tools", "Technologies",
  "Technical Skills", or similar into one flat list of individual skill/tool
  names.
- linkedin_url / portfolio_url: the full URL only if it is visibly printed as
  text on the page. If a link is only present as hyperlinked anchor text (e.g.
  a "LinkedIn" link with no visible URL string), return null rather than
  guessing the URL.

Rules:
- Do not invent information that is not present in the resume.
- Use null for any field genuinely not determinable from the resume, except
  experience_years, which should be 0 when there is no experience.
- Return valid JSON only, matching the response schema exactly.
PROMPT
        ];

        $data = [
            'inlineData' => [
                'mimeType' => 'application/pdf',
                'data' => $pdf
            ]
        ];

        $responseConfig = [
            'responseMimeType' => 'application/json',
            'responseSchema' => [
                'type' => 'OBJECT',
                'properties' => [
                    'first_name' => ['type' => 'STRING', 'description' => "Candidate's first name."],
                    'last_name' => ['type' => 'STRING', 'description' => "Candidate's last name."],
                    'email' => ['type' => 'STRING', 'description' => 'Email address, usually in the header next to an envelope icon.'],
                    'phone' => ['type' => 'STRING', 'description' => 'Phone number, usually in the header next to a phone icon.'],
                    'location' => ['type' => 'STRING', 'description' => 'City/region, usually in the header next to a pin icon.'],
                    'highest_qualification' => ['type' => 'STRING', 'description' => "The candidate's single highest completed or in-progress degree, exactly as written (e.g. \"Bachelor of Computer Applications (BCA)\")."],
                    'college' => ['type' => 'STRING', 'description' => 'Institution/university name tied to the highest qualification. Only if explicitly printed in the resume — never inferred.'],
                    'experience_years' => ['type' => 'NUMBER', 'description' => "Total professional experience (jobs + internships combined) in decimal years, computed from each role's date range using today's date for any open-ended \"Present\" role. 0 if there is no experience, never null."],
                    'current_company' => ['type' => 'STRING', 'description' => "Company of the candidate's most recent role (current if ongoing, otherwise their last/most recent past employer). Null only if no work experience exists at all."],
                    'current_designation' => ['type' => 'STRING', 'description' => "Job title of the candidate's most recent role, following the same current-or-most-recent rule as current_company."],
                    'skills' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING'], 'description' => 'Flat list of individual skills/tools/technologies, merged from any Skills, Tools, or Technologies sections.'],
                    'linkedin_url' => ['type' => 'STRING', 'description' => 'Full LinkedIn URL, only if visibly printed as text (not just a hyperlinked label).'],
                    'portfolio_url' => ['type' => 'STRING', 'description' => 'Full portfolio/personal website URL, only if visibly printed as text (not just a hyperlinked label).'],
                ],
            ],
        ];

        $response = Http::withHeaders($headers)
            // Gemini returns 503 "UNAVAILABLE" under transient high demand —
            // retry those (and dropped connections) but not 4xx errors, which
            // won't be fixed by retrying.
            ->retry(3, 1000, function ($exception, $request) {
                return $exception instanceof ConnectionException
                    || ($exception instanceof RequestException && $exception->response->status() >= 500);
            }, throw: false)
            ->post($url, [
                'contents' => [[
                    'parts' => [
                        $prompt,
                        $data
                    ]
                ]],
                'generationConfig' => $responseConfig,
            ]);

        if ($response->failed()) {
            \Log::error('Gemini resume parsing request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! $text) {
            return [];
        }

        $decoded = json_decode($text, true);

        return is_array($decoded) ? $this->normalize($decoded) : [];
    }

    
    private function normalize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = array_values(array_filter(
                    array_map(fn ($item) => $this->nullifyLiteral($item), $value),
                    fn ($item) => $item !== null && $item !== ''
                ));

                continue;
            }

            $data[$key] = $this->nullifyLiteral($value);
        }

        foreach (['linkedin_url', 'portfolio_url'] as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = Url::withScheme($data[$key]);
            }
        }

        return $data;
    }

    private function nullifyLiteral($value)
    {
        return is_string($value) && strtolower(trim($value)) === 'null' ? null : $value;
    }
}
