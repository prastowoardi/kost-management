<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2.5 rounded-xl font-semibold text-xs text-stone-700 uppercase tracking-widest bg-white ring-1 ring-stone-200 shadow-sm hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
