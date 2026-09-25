<?php

namespace App\Livewire\Hr\Candidates\Components;

use App\Services\ResumeParser;
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
        return ['resume' => 'required|file|mimes:pdf,doc,docx|max:5120'];
    }

    public function removeResume(): void
    {
        $this->resume = null;
    }

    public function processResume()
    {
        $this->validate();

        try {
            $parsed = app(ResumeParser::class)->parse($this->resume);
        } catch (\Throwable $e) {
            \Log::error('Resume parsing failed: ' . $e->getMessage());
            $parsed = [];
        }

       
        session(['pending_resume' => [
            'filename' => $this->resume->getFilename(),
            'data' => $parsed,
        ]]);

        return redirect()->route('hr.candidates.create');
    }

    public function render()
    {
        return view('livewire.hr.candidates.components.resume-upload');
    }
}
