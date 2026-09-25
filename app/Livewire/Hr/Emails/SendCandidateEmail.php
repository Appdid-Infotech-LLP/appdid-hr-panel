<?php

namespace App\Livewire\Hr\Emails;

use App\Models\Candidate;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Mounted once in layouts/hr.blade.php so it's available on every HR page.
 * Any page can open it by dispatching 'open-send-email-modal' with a
 * candidateId — see the sendEmail() placeholders in Candidates/Index and
 * Candidates/Show for the trigger side.
 */
class SendCandidateEmail extends Component
{
    public bool $show = false;

    public ?int $candidateId = null;

    public string $candidateName = '';

    public string $recipient = '';

    public string $subject = '';

    public string $message = '';

    #[On('open-send-email-modal')]
    public function open(int $candidateId): void
    {
        $candidate = Candidate::find($candidateId);

        if (! $candidate) {
            return;
        }

        $this->candidateId = $candidate->id;
        $this->candidateName = $candidate->first_name.' '.$candidate->last_name;
        $this->recipient = $candidate->email;
        $this->subject = '';
        $this->message = '';
        $this->show = true;
    }

    public function close(): void
    {
        $this->reset(['show', 'candidateId', 'candidateName', 'recipient', 'subject', 'message']);
    }

    /**
     * TODO — YOUR IMPLEMENTATION
     * e.g. return ['recipient' => 'required|email', 'subject' => 'required|string|max:255', 'message' => 'required|string'];
     */
    protected function rules(): array
    {
        return [];
    }

    public function sendEmail(): void
    {
        /*
         * TODO — YOUR IMPLEMENTATION
         *
         * 1. Validate the form: $this->validate();
         * 2. Build a Mailable (e.g. App\Mail\CandidateNotification) — you
         *    might want a set of templates for the standard cases (round
         *    scheduled, rescheduled, cancelled, reminder, selection,
         *    rejection) rather than always sending free-form text.
         * 3. Mail::to($this->recipient)->send(...);
         * 4. Log a candidate_activities entry ("Email sent: {subject}").
         * 5. Flash a success message.
         * 6. Close the modal: $this->close();
         */
    }

    public function render()
    {
        return view('livewire.hr.emails.send-candidate-email');
    }
}
