@props(['name', 'label' => null, 'options' => [], 'placeholder' => 'Select...', 'required' => false])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-slate-700">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <select
        id="{{ $name }}"
        wire:model="{{ $name }}"
        {{ $attributes->class([
            'w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-500',
            'border-rose-300 focus:border-rose-400 focus:ring-rose-100' => $errors->has($name),
            'border-slate-300 focus:border-brand-teal focus:ring-brand-teal/20' => ! $errors->has($name),
        ]) }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
        @endforeach
    </select>

    @error($name)
        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
    @enderror
</div>
