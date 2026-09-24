<aside
    class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-brand-ink transition-transform duration-200 ease-in-out peer-checked/sidebar:translate-x-0 lg:translate-x-0">
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-white/10 px-5">
        <img src="{{ asset('images/appdid-logo.png') }}" alt="Appdid Technologies" class="h-8 w-8 object-contain">
        <div class="leading-tight">
            <p class="text-sm font-semibold text-white">Appdid Technologies</p>
            <p class="text-xs text-teal-100/70">HR Panel</p>
        </div>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
        <div class="space-y-1">
            <x-hr.nav-link route="hr.dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 12l8.954-8.955a1.5 1.5 0 0 1 2.122 0L22.28 12M4.5 9.75V19.5a1.5 1.5 0 0 0 1.5 1.5h3.75v-6h4.5v6H18a1.5 1.5 0 0 0 1.5-1.5V9.75" />
                </svg>
                Dashboard
            </x-hr.nav-link>
        </div>

        <div class="space-y-1">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-teal-100/50">Recruitment</p>

            <x-hr.nav-link route="hr.candidates.index">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
                Candidates
            </x-hr.nav-link>

            <x-hr.nav-link route="hr.pipeline">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v12.75c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
                Pipeline
            </x-hr.nav-link>

            <x-hr.nav-link route="hr.rounds.index">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Rounds
            </x-hr.nav-link>

            <x-hr.nav-link route="hr.gantt">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h10.5M3.75 12h16.5m-16.5 5.25h7.5" />
                </svg>
                Gantt
            </x-hr.nav-link>
        </div>

        <div class="space-y-1">
            <x-hr.nav-link route="hr.calendar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                Calendar
            </x-hr.nav-link>

            <x-hr.nav-link route="hr.reports">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                </svg>
                Reports
            </x-hr.nav-link>

            <x-hr.nav-link route="hr.settings">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                Settings
            </x-hr.nav-link>
        </div>
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="flex items-center gap-3 rounded-lg bg-white/5 px-3 py-2.5">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-yellow text-sm font-semibold text-brand-ink">
                {{ Illuminate\Support\Str::initials(auth()->user()->name) }}
            </div>
            <div class="min-w-0 flex-1 leading-tight">
                <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-teal-100/60">{{ auth()->user()->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Log out"
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-teal-100/60 hover:bg-white/10 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
