<?php

namespace App\Livewire\Hr\Candidates;

use App\Support\CandidateOptions;
use App\Support\DemoCandidates;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.hr', ['title' => 'Edit Candidate'])]
class Edit extends Component
{
    use WithFileUploads;

    public int $candidateId;

    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $phone = '';

    public string $alternatePhone = '';

    public string $dateOfBirth = '';

    public string $gender = '';

    public string $location = '';

    public string $address = '';

    public string $highestQualification = '';

    public string $college = '';

    public string $experienceYears = '';

    public string $currentCompany = '';

    public string $currentDesignation = '';

    public string $currentSalary = '';

    public string $expectedSalary = '';

    public string $noticePeriod = '';

    /** @var array<int, string> */
    public array $skills = [];

    public string $linkedinUrl = '';

    public string $portfolioUrl = '';

    public $resume;

    public ?string $existingResumeFilename = null;

    public string $notes = '';

    /**
     * TODO — YOUR IMPLEMENTATION
     * Replace DemoCandidates::find() with Candidate::findOrFail($candidate)
     * once the model exists, and inject the model via route binding instead
     * of a raw id.
     */
    public function mount(int $candidateId): void
    {
        $data = DemoCandidates::find($candidateId);

        abort_unless($data, 404);

        $this->candidateId = $data['id'];
        $this->firstName = $data['first_name'];
        $this->lastName = $data['last_name'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->alternatePhone = (string) $data['alternate_phone'];
        $this->dateOfBirth = (string) $data['date_of_birth'];
        $this->gender = (string) $data['gender'];
        $this->location = (string) $data['location'];
        $this->address = (string) $data['address'];
        $this->highestQualification = (string) $data['highest_qualification'];
        $this->college = (string) $data['college'];
        $this->experienceYears = (string) $data['experience_years'];
        $this->currentCompany = (string) $data['current_company'];
        $this->currentDesignation = (string) $data['current_designation'];
        $this->currentSalary = (string) $data['current_salary'];
        $this->expectedSalary = (string) $data['expected_salary'];
        $this->noticePeriod = (string) $data['notice_period'];
        $this->skills = $data['skills'];
        $this->linkedinUrl = (string) $data['linkedin_url'];
        $this->portfolioUrl = (string) $data['portfolio_url'];
        $this->existingResumeFilename = $data['resume_filename'];
        $this->notes = (string) $data['notes'];
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

    /**
     * TODO — YOUR IMPLEMENTATION
     * Same as Create::rules(), plus ignore the current candidate's own row
     * in the unique email check: 'email' => ['unique:candidates,email,'.$this->candidateId],
     */
    protected function rules(): array
    {
        return [];
    }

    public function removeResume(): void
    {
        $this->resume = null;
    }

    public function removeExistingResume(): void
    {
        $this->existingResumeFilename = null;
    }

    public function removeSkill(string $skill): void
    {
        $this->skills = array_values(array_diff($this->skills, [$skill]));
    }

    public function save()
    {
        /*
         * TODO — YOUR IMPLEMENTATION
         *
         * 1. Validate the form: $this->validate();
         * 2. Store the new resume file if one was uploaded, replacing the old one.
         * 3. Update the candidate record:
         *    Candidate::findOrFail($this->candidateId)->update([...]);
         * 4. Log a candidate_activities entry ("Candidate details updated").
         * 5. Flash a success message.
         * 6. Redirect back to the candidate's detail page.
         */
    }

    public function render()
    {
        return view('livewire.hr.candidates.edit');
    }
}
