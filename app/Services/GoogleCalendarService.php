<?php

namespace App\Services;

use App\Models\CandidateRound;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventAttendee;
use Google\Service\Calendar\EventDateTime;

class GoogleCalendarService
{
    protected Calendar $service;

    protected string $calendarId;

    public function __construct()
    {
        $client = new Client;
        $client->setAuthConfig(base_path(config('services.google_calendar.credentials_path')));
        $client->addScope(Calendar::CALENDAR);

        $this->service = new Calendar($client);
        $this->calendarId = config('services.google_calendar.calendar_id');
    }

    public function createEvent(CandidateRound $round): string
    {
        $event = $this->buildEvent($round);

        $created = $this->service->events->insert($this->calendarId, $event, ['sendUpdates' => 'all']);

        return $created->getId();
    }

    public function updateEvent(string $eventId, CandidateRound $round): void
    {
        $this->service->events->update($this->calendarId, $eventId, $this->buildEvent($round), ['sendUpdates' => 'all']);
    }

    public function deleteEvent(string $eventId): void
    {
        $this->service->events->delete($this->calendarId, $eventId, ['sendUpdates' => 'all']);
    }

    /**
     * Google's generated model classes (Event, EventDateTime, EventAttendee)
     * have a final, zero-argument constructor in this version of the SDK —
     * `new Event([...])` throws "Too many arguments". Build them with
     * setters instead; that works across every version.
     */
    protected function buildEvent(CandidateRound $round): Event
    {
        $candidate = $round->candidate;

        $start = new EventDateTime;
        $start->setDateTime($round->schedule_at->toRfc3339String());
        $start->setTimeZone(config('app.timezone'));

        $end = new EventDateTime;
        $end->setDateTime($round->schedule_at->copy()->addMinutes(45)->toRfc3339String());
        $end->setTimeZone(config('app.timezone'));

        $attendees = [];

        $candidateAttendee = new EventAttendee;
        $candidateAttendee->setEmail($candidate->email);
        $attendees[] = $candidateAttendee;

        if ($round->interviewer?->email) {
            $interviewerAttendee = new EventAttendee;
            $interviewerAttendee->setEmail($round->interviewer->email);
            $attendees[] = $interviewerAttendee;
        }

        $event = new Event;
        $event->setSummary("{$round->type} — {$candidate->first_name} {$candidate->last_name}");
        $event->setDescription($round->notes);
        $event->setStart($start);
        $event->setEnd($end);
        $event->setLocation($round->mode === 'Virtual' ? $round->meeting_link : null);
        $event->setAttendees($attendees);

        return $event;
    }
}
