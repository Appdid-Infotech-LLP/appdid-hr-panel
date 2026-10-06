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
        is only safe when there really is an earlier page of this app, otherwise
        it could leave the site. That's the case once, in this tab, the user has
        either navigated inside the app (wire:navigate's own events) or arrived
        at a page from another page of the app (document.referrer). That fact is
        kept in sessionStorage so it survives a reload — a reload resets the page
        but not the browser history behind it. Otherwise the link's own
        (fallback) href is followed.

        wire:navigate starts its visit on mousedown/mouseup — *before* `click`
        fires — so the mousedown has to be intercepted too, or it would already
        be heading to the fallback by the time the click handler runs.

        data-navigate-once: wire:navigate re-runs body scripts on every page
        swap, which would stack up duplicate listeners.
    --}}
    <script data-navigate-once>
        if (!window.hrBackButtonReady) {
            window.hrBackButtonReady = true;

            const STORAGE_KEY = 'hr:in-app-history';

            // sessionStorage can throw (blocked storage, private modes) — then
            // we just fall back to remembering for this page load only.
            let inMemory = false;
            const remember = () => {
                inMemory = true;

                try { sessionStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
            };
            const remembered = () => {
                try { return inMemory || sessionStorage.getItem(STORAGE_KEY) === '1'; } catch (e) { return inMemory; }
            };

            document.addEventListener('livewire:navigate', (event) => {
                if (!event.detail?.history) {
                    remember();
                }
            });

            try {
                const referrer = new URL(document.referrer);

                if (referrer.origin === location.origin && referrer.href !== location.href) {
                    remember();
                }
            } catch (e) {}

            const canGoBack = () => window.history.length > 1 && remembered();

            const backLinkFor = (event) => {
                const link = event.target.closest?.('a[data-hr-back]');

                // Plain left-click only: leave ctrl/cmd/shift/middle-click (new tab/window) alone.
                if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                    return null;
                }

                return link;
            };

            // Capture phase + stopPropagation so wire:navigate's own handlers
            // on the link never start a navigation to the fallback.
            document.addEventListener('mousedown', (event) => {
                if (backLinkFor(event) && canGoBack()) {
                    event.stopPropagation();
                }
            }, true);

            document.addEventListener('click', (event) => {
                if (backLinkFor(event) && canGoBack()) {
                    event.preventDefault();
                    event.stopPropagation();
                    window.history.back();
                }
            }, true);
        }
    </script>
</body>
</html>
