@props(['name', 'label' => null, 'value' => null, 'placeholder' => 'Select date', 'maxDate' => null, 'disabled' => false])

@php
    $id = 'datepicker-' . str()->random(8);
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif

    {{--
        wire:ignore keeps Livewire from re-rendering over flatpickr's own DOM
        additions (the calendar popup).

        A plain inline <script> is used instead of @script/@endscript:
        @script de-duplicates by *compile-time file position*, so a second
        datepicker on the same page would silently fail to initialize. See
        the same note in x-hr.select2 for the full explanation, including
        why the window.flatpickr check below is needed for the same reason.
    --}}
    <div wire:ignore>
        <input
            type="text" id="{{ $id }}" placeholder="{{ $placeholder }}" autocomplete="off"
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-500"
        >

        <script>
            (() => {
                function init() {
                    const el = document.getElementById('{{ $id }}');

                    const disabled = @js((bool) $disabled);

                    const picker = window.flatpickr(el, {
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: 'M j, Y',
                        defaultDate: @js($value),
                        maxDate: @js($maxDate),
                        clickOpens: ! disabled,
                        onChange: (selectedDates, dateStr) => @this.set('{{ $name }}', dateStr),
                    });

                    // With altInput, the visible field is flatpickr's copy, not `el`.
                    if (disabled) {
                        picker.altInput.disabled = true;
                    }
                }

                if (window.flatpickr) {
                    init();
                } else {
                    document.addEventListener('DOMContentLoaded', init, { once: true });
                }
            })();
        </script>
    </div>

    @error($name)
        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
    @enderror
</div>
