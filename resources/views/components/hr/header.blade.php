@props(['title' => 'Dashboard'])

<header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-4 border-b border-slate-200 bg-white px-4 sm:px-6 lg:px-8">
    <label for="sidebar-toggle" class="-ml-1 flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 lg:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
        </svg>
    </label>

    <h1 class="text-lg font-semibold text-slate-900">{{ $title }}</h1>

    <div class="ml-auto flex items-center gap-3">
        <div class="relative hidden sm:block">
            <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <input
                type="search"
                placeholder="Search candidates..."
                class="w-64 rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-brand-teal focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-teal/20"
            >
        </div>

        <button type="button" class="relative flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
            <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-brand-yellow ring-2 ring-white"></span>
        </button>

        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-teal text-sm font-semibold text-white">
            HR
        </div>
    </div>
</header>
