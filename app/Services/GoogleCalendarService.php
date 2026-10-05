<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\CandidateRound;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\ConferenceData;
use Google\Service\Calendar\ConferenceSolutionKey;
use Google\Service\Calendar\CreateConferenceRequest;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventAttendee;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleCalendarService
{
    protected Calendar $service;

    // The event lands on whichever Google account the logged-in admin
    // connected — not a shared calendar, so always their own "primary".
    protected string $calendarId = 'primary';

    /**
     * @throws RuntimeException if the current admin hasn't connected Google
     *                          Calendar, or their connection has expired.
     */
    public function __construct()
    {
        $admin = Auth::user();

        if (! $admin instanceof Admin || ! $admin->hasGoogleCalendarConnected()) {
            throw new RuntimeException('Google Calendar is not connected for this account. Connect it from Settings first.');
        }

        $client = new Client;
        $client->setClientId(config('services.google_calendar.client_id'));
        $client->setClientSecret(config('services.google_calendar.client_secret'));
        $client->setAccessToken($admin->google_calendar_token);

        if ($client->isAccessTokenExpired()) {
            $refreshToken = $client->getRefreshToken();
            $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);

            if (isset($newToken['error'])) {
                throw new RuntimeException('Google Calendar connection expired. Please reconnect from Settings.');
            }

            // Google often omits refresh_token on a refresh response — it
            // only sends a new one on the original consent — so keep ours.
            $newToken['refresh_token'] ??= $refreshToken;

            $admin->update(['google_calendar_token' => $newToken]);
            $client->setAccessToken($newToken);
        }

        $this->service = new Calendar($client);
    }

    public function createEvent(CandidateRound $round): Event
    {
        $event = $this->buildEvent($round);

        return $this->service->events->insert($this->calendarId, $event, [
            'sendUpdates' => 'all',
            'conferenceDataVersion' => 1,
        ]);
    }

    public function updateEvent(string $eventId, CandidateRound $round): Event
    {
        return $this->service->events->update($this->calendarId, $eventId, $this->buildEvent($round), [
            'sendUpdates' => 'all',
            'conferenceDataVersion' => 1,
        ]);
    }

    /**
     * Pulls the generated meet.google.com link out of an Event returned by
     * createEvent()/updateEvent(). Null if the round isn't Virtual, or
     * Google hasn't attached conference data (e.g. still provisioning).
     */
    public static function meetLink(Event $event): ?string
    {
        $entryPoints = $event->getConferenceData()?->getEntryPoints() ?? [];

        foreach ($entryPoints as $entryPoint) {
            if ($entryPoint->getEntryPointType() === 'video') {
                return $entryPoint->getUri();
            }
        }

        return null;
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

   
        if ($round->mode === 'Virtual' && ! $round->meeting_link) {
            $solutionKey = new ConferenceSolutionKey;
            $solutionKey->setType('hangoutsMeet');

            $createRequest = new CreateConferenceRequest;
            $createRequest->setRequestId((string) Str::uuid());
            $createRequest->setConferenceSolutionKey($solutionKey);

            $conferenceData = new ConferenceData;
            $conferenceData->setCreateRequest($createRequest);

            $event->setConferenceData($conferenceData);
        }

        return $event;
    }
}
