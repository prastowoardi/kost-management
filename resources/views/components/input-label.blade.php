@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-stone-700']) }}>
    {{ $value ?? $slot }}
</label>
