@props(['name', 'label' => null, 'options' => [], 'value' => null, 'placeholder' => 'Select...', 'multiple' => false, 'tags' => false])

@php
    $id = 'select2-' . str()->random(8);

    // In tags/multiple mode, a previously chosen value might not be in
    // $options (e.g. a free-typed skill) — add it so it renders selected.
    $renderedOptions = $options;
    foreach ((array) $value as $selected) {
        if ($selected !== null && $selected !== '' && ! array_key_exists($selected, $renderedOptions)) {
            $renderedOptions[$selected] = $selected;
        }
    }
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif

    {{--
        wire:ignore stops Livewire from morphing this element on re-render,
        which would otherwise fight with select2's own DOM manipulation.
        The plugin is initialized once (below) and pushes changes back into
        the component with @this.set() — Livewire's documented pattern for
        wrapping jQuery-based UI libraries.

        This uses a plain inline <script> rather than @script/@endscript:
        @script de-duplicates by *compile-time file position*, so when this
        same component is used more than once on a page (e.g. two select2
        fields), every instance after the first gets silently dropped. A
        plain script tag has no such de-dup and runs once per instance,
        which is what wire:ignore already guarantees on top of it.

        Trade-off: unlike @script, a plain tag runs immediately as the
        parser hits it — before app.js's deferred `type="module"` script has
        attached select2 to jQuery. The jQuery-ready check below waits for
        that on a fresh page load; on a wire:navigate swap jQuery is already
        loaded, so it runs immediately.
    --}}
    <div wire:ignore>
        <select id="{{ $id }}" {{ $multiple ? 'multiple' : '' }} class="hr-select2 w-full" data-placeholder="{{ $placeholder }}">
            @if (! $multiple)
                <option></option>
            @endif
            @foreach ($renderedOptions as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
            @endforeach
        </select>

        <script>
            (() => {
                function init() {
                    const el = document.getElementById('{{ $id }}');
                    const initial = @js($value);

                    $(el).select2({ width: '100%', placeholder: @js($placeholder), allowClear: ! @js($multiple), tags: @js($tags) });

                    if (initial !== null && initial !== undefined) {
                        $(el).val(initial).trigger('change.select2');
                    }

                    $(el).on('change', function () {
                        @this.set('{{ $name }}', $(this).val());
                    });
                }

                if (window.jQuery && window.jQuery.fn.select2) {
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
