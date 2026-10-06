<?php

namespace App\Mail;

use App\Models\CandidateRound;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RoundScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CandidateRound $round,
        public ?CarbonInterface $previousScheduleAt = null,
    ) {}

    public function envelope(): Envelope
    {
        $verb = $this->previousScheduleAt ? 'has been rescheduled' : 'is scheduled';

        return new Envelope(
            subject: "Your {$this->round->type} {$verb} — Appdid Technologies",
        );
    }

    public function content(): Content
    {
        $candidate = $this->round->candidate;

        return new Content(
            view: 'emails.round-scheduled',
            with: [
                'candidateName' => trim("{$candidate->first_name} {$candidate->last_name}"),
                'roundType' => $this->round->type,
                'scheduleAt' => $this->round->schedule_at,
                'previousScheduleAt' => $this->previousScheduleAt,
                'mode' => $this->round->mode,
                'meetingLink' => $this->round->meeting_link,
                'interviewerName' => $this->round->interviewer?->name,
                'notes' => $this->round->notes,
            ],
        );
    }
}
