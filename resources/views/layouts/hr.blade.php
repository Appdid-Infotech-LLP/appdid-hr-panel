<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} · Appdid HR Panel</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-slate-50 text-slate-800">
    <div class="flex min-h-screen">
        {{-- Checkbox-driven mobile sidebar toggle: no JavaScript required. --}}
        <input type="checkbox" id="sidebar-toggle" class="peer/sidebar hidden" aria-hidden="true">

        <x-hr.sidebar />

        <label
            for="sidebar-toggle"
            class="fixed inset-0 z-30 hidden bg-slate-900/50 peer-checked/sidebar:block lg:hidden"
            aria-hidden="true"
        ></label>

        <div class="flex min-h-screen w-full flex-1 flex-col lg:pl-64">
            <x-hr.header :title="$title ?? 'Dashboard'" />

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        {{ session('warning') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
