<?php

namespace App\Livewire\Hr\Candidates;

use App\Support\CandidateOptions;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.hr', ['title' => 'Add Candidate'])]
class Create extends Component
{
    use WithFileUploads;

    // Personal details
    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $phone = '';

    public string $alternatePhone = '';

    public string $dateOfBirth = '';

    public string $gender = '';

    public string $location = '';

    public string $address = '';

    // Professional details
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

    // Links & documents
    public string $linkedinUrl = '';

    public string $portfolioUrl = '';

    public $resume;

    public string $notes = '';

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
     * Fill these in once you're ready to validate the form, e.g.:
     *   'firstName' => ['required', 'string', 'max:255'],
     *   'email' => ['required', 'email', 'unique:candidates,email'],
     */
    protected function rules(): array
    {
        return [];
    }

    public function removeResume(): void
    {
        $this->resume = null;
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
         * 2. Store the resume file (if present) on a disk, e.g.
         *    $path = $this->resume?->store('resumes', 'public');
         * 3. Create the candidate record:
         *    Candidate::create([...$this->only([...]), 'resume_path' => $path]);
         * 4. Log a candidate_activities entry ("Candidate added").
         * 5. Flash a success message.
         * 6. Redirect to the candidate's detail page or back to the list.
         */
    }

    public function render()
    {
        return view('livewire.hr.candidates.create');
    }
}
