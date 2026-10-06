@props(['fromName', 'toName', 'from' => null, 'to' => null, 'placeholder' => 'Select date range'])

@php
    $id = 'daterange-' . str()->random(8);
@endphp

{{--
    Date range filter: one flatpickr field in range mode that drives two
    Livewire properties (`fromName` / `toName`, Y-m-d strings, '' = unset).

    Same constraints as x-hr.datepicker: wire:ignore keeps Livewire off
    flatpickr's DOM, and a plain inline <script> (not @script) so several
    instances on one page all initialise.

    Two-way: picking/clearing pushes both values to the server; a server-side
    reset ("Clear all filters") is pulled back in through $watch, with
    setDate(..., false) / clear(false) so it doesn't echo back as a change.
--}}
<div {{ $attributes }}>
    <div wire:ignore class="relative">
        <input type="text" id="{{ $id }}" placeholder="{{ $placeholder }}" autocomplete="off"
            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 pr-9 text-sm text-slate-700 placeholder:text-slate-400 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">

        <button type="button" id="{{ $id }}-clear" title="Clear dates" aria-label="Clear dates"
            class="absolute top-1/2 right-2 hidden h-6 w-6 -translate-y-1/2 items-center justify-center rounded text-slate-400 hover:text-slate-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>

        <script>
            (() => {
                function init() {
                    const el = document.getElementById('{{ $id }}');
                    const clearButton = document.getElementById('{{ $id }}-clear');
                    const fromName = @js($fromName);
                    const toName = @js($toName);

                    const showClear = (picker) => {
                        clearButton.classList.toggle('hidden', picker.selectedDates.length === 0);
                        clearButton.classList.toggle('flex', picker.selectedDates.length > 0);
                    };

                    const picker = window.flatpickr(el, {
                        mode: 'range',
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: 'M j, Y',
                        defaultDate: [@js($from), @js($to)].filter(Boolean),
                        onReady: (dates, str, instance) => showClear(instance),
                        onChange: (dates, str, instance) => {
                            showClear(instance);

                            // Mid-selection (only the first date picked) —
                            // wait for the second before filtering.
                            if (dates.length === 1) {
                                return;
                            }

                            const [start, end] = dates.length === 2
                                ? [instance.formatDate(dates[0], 'Y-m-d'), instance.formatDate(dates[1], 'Y-m-d')]
                                : ['', ''];

                            // Defer the first write so both land in one request.
                            @this.set(fromName, start, false);
                            @this.set(toName, end);
                        },
                    });

                    clearButton.addEventListener('click', () => picker.clear());

                    const pullFromServer = () => {
                        if (! document.body.contains(el)) {
                            return;
                        }

                        const component = @this;
                        const wanted = [component.get(fromName), component.get(toName)].filter(Boolean);
                        const current = picker.selectedDates.map((date) => picker.formatDate(date, 'Y-m-d'));

                        // A lone "from" (hand-edited URL) is shown as-is; our own
                        // writes already match the picker, so they skip this.
                        if (JSON.stringify(wanted) === JSON.stringify(current)) {
                            return;
                        }

                        wanted.length ? picker.setDate(wanted, false) : picker.clear(false);
                        showClear(picker);
                    };

                    // On a wire:navigate page swap this script runs *before*
                    // Livewire has registered the new page's components, so
                    // the component can't be looked up yet — wait for it.
                    whenComponentReady((component) => {
                        component.$watch(fromName, pullFromServer);
                        component.$watch(toName, pullFromServer);
                    });
                }

                function whenComponentReady(callback, attempt = 0) {
                    let component = null;

                    try {
                        component = @this;
                    } catch (e) {}

                    if (component) {
                        callback(component);
                    } else if (attempt < 60) {
                        setTimeout(() => whenComponentReady(callback, attempt + 1), 50);
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
</div>
