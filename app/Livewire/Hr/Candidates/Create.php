<?php

namespace App\Livewire\Hr\Candidates;

use App\Helpers\FileUploader;
use App\Models\Candidate;
use App\Services\ResumeParser;
use App\Support\CandidateOptions;
use App\Support\Url;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

#[Layout('layouts.hr', ['title' => 'Add Candidate'])]
class Create extends Component
{
    use WithFileUploads;

    // Personal details
    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $phone = '';

    public string $dateOfBirth = '';

    public string $gender = '';

    public string $location = '';

    public string $address = '';

    // Professional details
    public string $highestQualification = '';

    // Free-text value shown when highestQualification is "Other" — either
    // picked manually, or because a parsed resume's qualification didn't
    // match any of the dropdown options.
    public string $highestQualificationOther = '';

    public string $college = '';

    public string $experienceYears = '';

    public string $experienceMonths = '';

    public string $currentCompany = '';

    public string $currentDesignation = '';

    public string $currentSalary = '';

    public string $expectedSalary = '';

    public string $noticePeriod = '';

    /** @var array<int, string> */
    public array $skills = [];

    // Call follow-up details — gathered after HR speaks with the candidate
    public string $contactedOn = '';

    /** @var array<int, string> */
    public array $techStack = [];

    public bool $agreedToBond = false;

    public string $expectedJoiningDate = '';

    public string $reasonForLeaving = '';

    // Links & documents
    public string $linkedinUrl = '';

    public string $portfolioUrl = '';

    public $resume;

    public string $notes = '';

    // Set once a resume has been read: 'filled' (we found something) or
    // 'failed' (couldn't be read/parsed), so the page can say which.
    public string $resumeParseStatus = '';

    /**
     * Runs as soon as a resume finishes uploading: read it and pre-fill the
     * form, so HR doesn't need a separate upload step before this page.
     * Only blank fields are filled, so anything already typed is kept.
     */
    public function updatedResume(): void
    {
        $this->resumeParseStatus = '';

        if (! $this->resume) {
            return;
        }

        try {
            $this->validateOnly('resume');
        } catch (ValidationException $e) {
            $this->resume = null;

            throw $e;
        }

        try {
            $data = app(ResumeParser::class)->parse($this->resume);
        } catch (Throwable $e) {
            Log::error('Resume parsing failed: '.$e->getMessage());
            $data = [];
        }

        $this->resumeParseStatus = $this->applyParsedData($data) ? 'filled' : 'failed';
    }

    /**
     * @return bool whether anything was actually filled in
     */
    private function applyParsedData(array $data): bool
    {
        $filled = false;

        $fill = function (string $property, string $key) use ($data, &$filled): void {
            $value = $data[$key] ?? null;

            if ($this->{$property} === '' && is_scalar($value) && trim((string) $value) !== '') {
                $this->{$property} = trim((string) $value);
                $filled = true;
            }
        };

        $fill('firstName', 'first_name');
        $fill('lastName', 'last_name');
        $fill('email', 'email');
        $fill('phone', 'phone');
        $fill('location', 'location');
        $fill('college', 'college');
        $fill('currentCompany', 'current_company');
        $fill('currentDesignation', 'current_designation');
        $fill('linkedinUrl', 'linkedin_url');
        $fill('portfolioUrl', 'portfolio_url');

        if ($this->highestQualification === '' && ! empty($data['highest_qualification'])) {
            $this->applyParsedQualification((string) $data['highest_qualification']);
            $filled = true;
        }

        if ($this->experienceYears === '' && $this->experienceMonths === '' && isset($data['experience_years'])) {
            $this->applyParsedExperience((float) $data['experience_years']);
            $filled = true;
        }

        if ($this->skills === [] && ! empty($data['skills']) && is_array($data['skills'])) {
            $this->skills = array_values(array_filter($data['skills'], 'is_string'));
            $filled = true;
        }

        return $filled;
    }

    /**
     * If a parsed resume's qualification exactly matches one of the dropdown
     * options, select it directly. Otherwise, fall back to "Other" and keep
     * the detected text so it isn't silently dropped.
     */
    private function applyParsedQualification(?string $qualification): void
    {
        if (! $qualification) {
            return;
        }

        $match = collect(array_keys(CandidateOptions::qualifications()))
            ->first(fn (string $option) => $option !== 'Other' && strcasecmp($option, $qualification) === 0);

        if ($match) {
            $this->highestQualification = $match;

            return;
        }

        $this->highestQualification = 'Other';
        $this->highestQualificationOther = $qualification;
    }

    /**
     * A parsed resume gives total experience as decimal years (e.g. 1.5) —
     * split it into the whole-years and months the UI collects separately.
     */
    private function applyParsedExperience(float $totalYears): void
    {
        $years = (int) floor($totalYears);
        $months = (int) round(($totalYears - $years) * 12);

        if ($months === 12) {
            $years++;
            $months = 0;
        }

        $this->experienceYears = (string) $years;
        $this->experienceMonths = (string) $months;
    }

    public function genders(): array
    {
        return CandidateOptions::genders();
    }

    public function qualifications(): array
    {
        return CandidateOptions::qualifications();
    }

    public function noticePeriods(): array
    {
        return CandidateOptions::noticePeriods();
    }

    public function locations(): array
    {
        return CandidateOptions::locations();
    }

    public function skillSuggestions(): array
    {
        return CandidateOptions::skillSuggestions();
    }

    protected function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:candidates,email'],
            'phone' => ['required', 'string', 'max:255'],
            'dateOfBirth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string'],
            'location' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'highestQualification' => ['nullable', 'string'],
            'highestQualificationOther' => ['required_if:highestQualification,Other', 'nullable', 'string', 'max:255'],
            'college' => ['nullable', 'string', 'max:255'],
            'experienceYears' => ['nullable', 'numeric', 'min:0'],
            'experienceMonths' => ['nullable', 'numeric', 'min:0', 'max:11'],
            'currentCompany' => ['nullable', 'string', 'max:255'],
            'currentDesignation' => ['nullable', 'string', 'max:255'],
            'currentSalary' => ['nullable', 'string', 'max:255'],
            'expectedSalary' => ['nullable', 'string', 'max:255'],
            'noticePeriod' => ['nullable', 'string'],
            'skills' => ['array'],
            'contactedOn' => ['nullable', 'date'],
            'techStack' => ['array'],
            'agreedToBond' => ['boolean'],
            'expectedJoiningDate' => ['nullable', 'date'],
            'reasonForLeaving' => ['nullable', 'string'],
            'linkedinUrl' => ['nullable', 'url', 'max:255'],
            'portfolioUrl' => ['nullable', 'url', 'max:255'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function removeResume(): void
    {
        $this->resume = null;
        $this->resumeParseStatus = '';
    }

    public function save()
    {
        $this->linkedinUrl = (string) Url::withScheme($this->linkedinUrl);
        $this->portfolioUrl = (string) Url::withScheme($this->portfolioUrl);

        $this->validate();

        // The resume only gets persisted to permanent storage here, at save
        // time — up to this point it's just sat in Livewire's temp disk.
        $resumeUrl = $this->resume
            ? FileUploader::uploadFile($this->resume, 'candidates/resumes')
            : null;

        $highestQualification = $this->highestQualification === 'Other'
            ? $this->highestQualificationOther
            : $this->highestQualification;

        // The UI collects years + months separately; combine them back into
        // the single decimal-years column (decimal(4,1)) the rest of the app
        // expects. Blank in both fields means "unknown", not "zero".
        $experienceYears = ($this->experienceYears === '' && $this->experienceMonths === '')
            ? null
            : round((float) ($this->experienceYears ?: 0) + ((float) ($this->experienceMonths ?: 0) / 12), 1);

        Candidate::create([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName ?: null,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->dateOfBirth ?: null,
            'gender' => $this->gender ?: null,
            'location' => $this->location ?: null,
            'address' => $this->address ?: null,
            'highest_qualification' => $highestQualification ?: null,
            'college' => $this->college ?: null,
            'experience_years' => $experienceYears,
            'current_company' => $this->currentCompany ?: null,
            'current_designation' => $this->currentDesignation ?: null,
            'current_salary' => $this->currentSalary ?: null,
            'expected_salary' => $this->expectedSalary ?: null,
            'notice_period' => $this->noticePeriod ?: null,
            'skills' => $this->skills,
            'contacted_on' => $this->contactedOn ?: null,
            'tech_stack' => $this->techStack,
            'agreed_to_bond' => $this->agreedToBond,
            'expected_joining_date' => $this->expectedJoiningDate ?: null,
            'reason_for_leaving' => $this->reasonForLeaving ?: null,
            'linkedin_url' => $this->linkedinUrl ?: null,
            'portfolio_url' => $this->portfolioUrl ?: null,
            'resume_path' => $resumeUrl,
            'notes' => $this->notes ?: null,
        ]);

        session()->flash('success', 'Candidate added successfully.');

        return redirect()->route('hr.candidates.index');
    }

    public function render()
    {
        return view('livewire.hr.candidates.create');
    }
}
