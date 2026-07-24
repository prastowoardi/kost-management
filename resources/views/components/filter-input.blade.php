@props(['name', 'label' => null, 'placeholder' => 'Cari...'])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">{{ $label }}</label>
    @endif
    <input
        type="text"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ request($name) }}"
        placeholder="{{ $placeholder }}"
        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-slate-300 focus:border-slate-400 outline-none transition placeholder:text-slate-300"
    >
</div>
