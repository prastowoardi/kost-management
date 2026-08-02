@props(['name', 'label' => null, 'placeholder' => 'Cari...'])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-stone-400">{{ $label }}</label>
    @endif
    <input
        type="text"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ request($name) }}"
        placeholder="{{ $placeholder }}"
        class="w-full rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm text-stone-800 shadow-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500 placeholder:text-stone-300"
    >
</div>
