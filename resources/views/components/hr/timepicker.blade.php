@props(['name', 'label' => null, 'value' => null, 'placeholder' => 'Select time', 'disabled' => false])

@php
    $id = 'timepicker-' . str()->random(8);
@endphp

{{--
    Time-only flatpickr. Shown as 12-hour ("02:30 PM"), stored in the Livewire
    property as 24-hour "H:i" ("14:30") — the format the rounds validate
    against, same as the native <input type="time"> it replaces.

    Same constraints as x-hr.datepicker: wire:ignore keeps Livewire off
    flatpickr's DOM, plain inline <script> (not @script) so several instances
    on a page all initialise. The error border is applied from the wrapper
    (outside wire:ignore) because the input itself is never re-rendered.
--}}
<div @class(['[&_input]:border-rose-300 [&_input]:focus:ring-rose-100' => $errors->has($name)])>
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif

    <div wire:ignore>
        <input type="text" id="{{ $id }}" placeholder="{{ $placeholder }}" autocomplete="off"
            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-500">

        <script>
            (() => {
                function init() {
                    const el = document.getElementById('{{ $id }}');
                    const disabled = @js((bool) $disabled);

                    const picker = window.flatpickr(el, {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: 'H:i',
                        altInput: true,
                        altFormat: 'h:i K',
                        time_24hr: false,
                        minuteIncrement: 5,
                        defaultDate: @js($value),
                        clickOpens: ! disabled,
                        onChange: (selectedDates, timeStr) => @this.set('{{ $name }}', timeStr),
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
