@props(['name', 'label' => null, 'options' => [], 'placeholder' => 'Semua', 'auto' => false])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">{{ $label }}</label>
    @endif
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @if($auto) onchange="this.form.submit()" @endif
        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-slate-300 focus:border-slate-400 outline-none transition bg-white"
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $value => $display)
            <option value="{{ $value }}" {{ request($name) == $value ? 'selected' : '' }}>{{ $display }}</option>
        @endforeach
    </select>
</div>
