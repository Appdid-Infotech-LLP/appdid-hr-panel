<?php

namespace App\Livewire\Hr\Candidates\Components;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.hr', ['title' => 'Upload Resume'])]
class ResumeUpload extends Component
{
    use WithFileUploads;

    public $resume;

    /**
     * TODO — YOUR IMPLEMENTATION
     * e.g. return ['resume' => 'required|file|mimes:pdf,doc,docx|max:5120'];
     */
    protected function rules(): array
    {
        return [];
    }

    public function removeResume(): void
    {
        $this->resume = null;
    }

    public function processResume()
    {
        /*
         * TODO — YOUR IMPLEMENTATION
         *
         * 1. Validate the upload: $this->validate();
         * 2. Store the file: $path = $this->resume->store('resumes', 'public');
         * 3. (Optional) Parse the resume to prefill fields — pick a parsing
         *    package or an AI service; this is intentionally not chosen for you.
         * 4. Either create the candidate directly, or redirect to the manual
         *    Create form with the extracted fields prefilled so HR can review
         *    them before saving.
         * 5. Log a candidate_activities entry ("Candidate added via resume upload").
         * 6. Flash a success message and redirect to the candidate list or profile.
         */
    }

    public function render()
    {
        return view('livewire.hr.candidates.components.resume-upload');
    }
}
