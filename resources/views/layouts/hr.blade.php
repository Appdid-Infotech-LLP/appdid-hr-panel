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

    <livewire:hr.emails.send-candidate-email />

    @livewireScripts

    {{--
        Back buttons (<x-hr.back-button>) go to the previous page. history.back()
        is only safe once the user has navigated inside the app at least once
        in this tab — otherwise it could leave the site — so count forward
        navigations and let the link's own (fallback) href handle the rest.
        data-navigate-once: wire:navigate re-runs body scripts on every page
        swap, which would stack up duplicate listeners.
    --}}
    <script data-navigate-once>
        if (!window.hrBackButtonReady) {
            window.hrBackButtonReady = true;
            window.hrForwardNavigations = 0;

            document.addEventListener('livewire:navigate', (event) => {
                if (!event.detail?.history) {
                    window.hrForwardNavigations++;
                }
            });

            // Capture phase + stopPropagation so wire:navigate's own handler
            // on the link never starts a second navigation to the fallback.
            document.addEventListener('click', (event) => {
                const link = event.target.closest?.('a[data-hr-back]');

                if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                    return;
                }

                if (window.hrForwardNavigations > 0 && window.history.length > 1) {
                    event.preventDefault();
                    event.stopPropagation();
                    window.history.back();
                }
            }, true);
        }
    </script>
</body>
</html>
