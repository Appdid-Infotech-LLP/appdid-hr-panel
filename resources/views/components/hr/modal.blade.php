@props(['show' => false, 'title' => null, 'maxWidth' => 'max-w-lg'])

{{--
    Reusable modal shell. By convention, the enclosing Livewire component
    must define a `close()` method — the backdrop and the × button both
    call it directly, so every modal built on this behaves the same way.
--}}
@if ($show)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="close"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div @class(['relative w-full rounded-xl bg-white shadow-xl', $maxWidth])>
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                    <button type="button" wire:click="close" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-5 py-5">
                    {{ $slot }}
                </div>

                @isset($footer)
                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-5 py-4">
                        {{ $footer }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
@endif
