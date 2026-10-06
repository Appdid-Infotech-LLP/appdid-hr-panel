<?php

namespace App\Jobs;

use App\Mail\RoundScheduledMail;
use App\Models\CandidateRound;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Dispatched from Rounds\Schedule::scheduleRound() once a round (and its
 * Google Meet link, if virtual) is saved, and from Rounds\Edit when a round
 * is rescheduled — pass the previous time then, so the email says so. Runs
 * on the queue so a slow mail transport never holds up the HR panel's response.
 */
class SendRoundScheduledEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public CandidateRound $round,
        public ?CarbonInterface $previousScheduleAt = null,
    ) {}

    public function handle(): void
    {
        $candidate = $this->round->candidate;

        if (! $candidate?->email) {
            return;
        }

        Mail::to($candidate->email)->send(new RoundScheduledMail($this->round, $this->previousScheduleAt));
    }
}
