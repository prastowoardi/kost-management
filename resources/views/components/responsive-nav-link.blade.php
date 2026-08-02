@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-xl px-4 py-2.5 text-start text-sm font-semibold text-brand-700 bg-brand-50 ring-1 ring-brand-100 focus:outline-none focus:text-brand-800 focus:bg-brand-100 transition duration-150 ease-in-out'
            : 'block w-full rounded-xl px-4 py-2.5 text-start text-sm font-medium text-stone-600 hover:text-stone-900 hover:bg-stone-50 focus:outline-none focus:text-stone-900 focus:bg-stone-50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
