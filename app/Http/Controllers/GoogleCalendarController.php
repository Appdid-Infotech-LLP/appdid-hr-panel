<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Calendar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleCalendarController extends Controller
{
    protected function client(): Client
    {
        $client = new Client;
        $client->setClientId(config('services.google_calendar.client_id'));
        $client->setClientSecret(config('services.google_calendar.client_secret'));
        $client->setRedirectUri(route('hr.google-calendar.callback'));
        $client->addScope(Calendar::CALENDAR_EVENTS);
        // offline + consent together are what guarantee Google actually
        // hands back a refresh_token — without "consent" it only does so
        // the very first time an account ever authorizes this app.
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return $client;
    }

    public function connect(): RedirectResponse
    {
        return redirect($this->client()->createAuthUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect()->route('hr.settings')->with('warning', 'Google Calendar connection was cancelled.');
        }

        $token = $this->client()->fetchAccessTokenWithAuthCode((string) $request->query('code'));

        if (isset($token['error'])) {
            return redirect()->route('hr.settings')->with('warning', 'Could not connect Google Calendar: '.($token['error_description'] ?? $token['error']));
        }

        if (empty($token['refresh_token'])) {
            // Google only sends a refresh_token on the very first consent for
            // this app+account. If someone connects, disconnects, then
            // reconnects, a stale Google-side consent can skip it even with
            // prompt=consent. Without one we can't silently refresh later,
            // so don't save a token that would just expire in ~1 hour.
            return redirect()->route('hr.settings')->with(
                'warning',
                'Google did not grant a long-lived connection. Visit https://myaccount.google.com/permissions, remove access for this app, then try connecting again.'
            );
        }

        try {
            Auth::user()->update(['google_calendar_token' => $token]);
        } catch (\Throwable $e) {
            \Log::error('Failed to save Google Calendar token: '.$e->getMessage());

            return redirect()->route('hr.settings')->with('warning', 'Google Calendar authorized, but the token could not be saved. Check storage/logs/laravel.log.');
        }

        return redirect()->route('hr.settings')->with('success', 'Google Calendar connected.');
    }

    public function disconnect(): RedirectResponse
    {
        Auth::user()->update(['google_calendar_token' => null]);

        return redirect()->route('hr.settings')->with('success', 'Google Calendar disconnected.');
    }
}
