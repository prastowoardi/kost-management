@props(['name', 'label' => null, 'value' => null, 'type' => 'date'])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-stone-400">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ $value ?? request($name) }}"
        onclick="this.showPicker()"
        class="w-full cursor-pointer rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm text-stone-800 shadow-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500"
    >
</div>
