@props(['name', 'label' => null, 'placeholder' => null, 'rows' => 4])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif

    <textarea
        id="{{ $name }}"
        wire:model="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->class([
            'w-full rounded-lg border px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2',
            'border-rose-300 focus:border-rose-400 focus:ring-rose-100' => $errors->has($name),
            'border-slate-300 focus:border-brand-teal focus:ring-brand-teal/20' => ! $errors->has($name),
        ]) }}
    >{{ $slot }}</textarea>

    @error($name)
        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
    @enderror
</div>
