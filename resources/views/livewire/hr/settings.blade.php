<div class="max-w-2xl space-y-6">
    <div>
        <h2 class="text-xl font-semibold text-slate-900">Settings</h2>
        <p class="mt-1 text-sm text-slate-500">Connect your own Google account so interview events land on your calendar.</p>
    </div>

    <x-hr.section-card title="Google Calendar">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <x-hr.badge :status="$this->isGoogleCalendarConnected() ? 'Connected' : 'Not Connected'" />
                <p class="mt-1.5 text-xs text-slate-500">
                    @if ($this->isGoogleCalendarConnected())
                        Interview events are created on your Google Calendar when you schedule or edit a round.
                    @else
                        Connect your Google account to let the HR panel create interview events directly on your calendar.
                    @endif
                </p>
            </div>

            @if ($this->isGoogleCalendarConnected())
                <form method="POST" action="{{ route('hr.google-calendar.disconnect') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-rose-200 px-4 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50">
                        Disconnect
                    </button>
                </form>
            @else
                {{-- Plain link, not wire:navigate — this has to leave the app for Google's own consent screen. --}}
                <a href="{{ route('hr.google-calendar.connect') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-teal px-4 py-2 text-sm font-medium text-white hover:bg-brand-teal-dark">
                    Connect Google Calendar
                </a>
            @endif
        </div>
    </x-hr.section-card>
</div>
