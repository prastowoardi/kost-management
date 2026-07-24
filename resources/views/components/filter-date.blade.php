@props(['name', 'label' => null, 'value' => null])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">{{ $label }}</label>
    @endif
    <input
        type="date"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ $value ?? request($name) }}"
        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-slate-300 focus:border-slate-400 outline-none transition"
    >
</div>
