<div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center">
    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-yellow/20 text-brand-yellow-dark">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
    </div>
    <h3 class="mt-4 text-base font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-1 max-w-sm text-sm text-slate-500">
        This section is built in {{ $phase }} of the project plan. Ask to continue to that phase when you're ready.
    </p>
</div>
