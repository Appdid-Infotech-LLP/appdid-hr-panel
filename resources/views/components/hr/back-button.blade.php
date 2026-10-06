@props(['fallback'])

{{--
    Back arrow. Goes to the page the user actually came from (the layout's
    click handler calls history.back() for [data-hr-back]); `fallback` is only
    used when there's no earlier in-app page — e.g. the page was opened
    directly from a bookmark or a new tab — and it's also the plain href, so
    middle-click / open-in-new-tab / no-JS still behave.
--}}
<a href="{{ $fallback }}" wire:navigate data-hr-back title="Back" aria-label="Back"
    {{ $attributes->class('flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50') }}>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
    </svg>
</a>
