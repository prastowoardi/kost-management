@props(['name', 'label' => null, 'options' => [], 'placeholder' => 'Semua', 'auto' => false])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-stone-400">{{ $label }}</label>
    @endif
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @if($auto) data-auto-submit @endif
        class="w-full cursor-pointer rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm text-stone-800 shadow-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500"
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $value => $display)
            <option value="{{ $value }}" {{ request($name) == $value ? 'selected' : '' }}>{{ $display }}</option>
        @endforeach
    </select>
</div>
