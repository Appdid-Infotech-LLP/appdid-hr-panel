@props(['name', 'label' => null, 'placeholder' => null, 'rows' => 4, 'hint' => null, 'hintIcon' => null])

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

    @if ($hint)
        <p class="mt-1.5 flex items-start gap-1.5 text-xs text-slate-500">
            @if ($hintIcon === 'mail')
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-px h-3.5 w-3.5 shrink-0 text-brand-teal" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
            @elseif ($hintIcon === 'lock')
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-px h-3.5 w-3.5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            @endif
            <span>{{ $hint }}</span>
        </p>
    @endif
</div>
