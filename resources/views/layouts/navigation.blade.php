<!-- Mobile overlay -->
<div x-cloak x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-40 bg-stone-900/50 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

<!-- Sidebar -->
<aside
    class="fixed inset-y-0 left-0 z-50 flex w-[var(--sidebar-w,288px)] max-w-[85vw] flex-col overflow-y-auto border-r border-stone-200/70 bg-white shadow-lift transition-transform duration-300 ease-in-out lg:sticky lg:top-0 lg:z-30 lg:h-screen lg:translate-x-0 lg:shadow-none"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <!-- Brand -->
    <div class="flex h-16 shrink-0 items-center justify-between border-b border-stone-100 px-5">
        <a href="{{ route('dashboard') }}" class="group flex items-center gap-2.5">
            <img src="{{ asset('serrata.png') }}" alt="Serrata Kost" class="h-9 w-9 rounded-xl object-cover shadow-soft transition-transform duration-200 group-hover:scale-105">
            <span class="text-lg font-extrabold tracking-tight text-stone-900">
                Serrata <span class="text-brand-600">Kost</span>
            </span>
        </a>
        <button @click="sidebarOpen = false" class="rounded-xl p-2 text-stone-400 transition hover:bg-stone-100 hover:text-stone-700 lg:hidden">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Menu -->
    <div class="flex-1 px-3 py-5">

        <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-widest text-stone-400">Menu Utama</p>
        <nav class="space-y-1">
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <x-slot name="icon">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"/></svg>
                </x-slot>
                {{ __('Dashboard') }}
            </x-sidebar-link>

            <x-sidebar-link :href="route('rooms.index')" :active="request()->routeIs('rooms.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </x-slot>
                {{ __('Kamar') }}
            </x-sidebar-link>

            <x-sidebar-link :href="route('calendar')" :active="request()->routeIs('calendar')">
                <x-slot name="icon">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </x-slot>
                {{ __('Kalender') }}
            </x-sidebar-link>

            <x-sidebar-link :href="route('tenants.index')" :active="request()->routeIs('tenants.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </x-slot>
                {{ __('Penghuni') }}
            </x-sidebar-link>

            <x-sidebar-link :href="route('facilities.index')" :active="request()->routeIs('facilities.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9V6a2 2 0 00-2-2H7a2 2 0 00-2 2v3m14 0V19a2 2 0 01-2 2H7a2 2 0 01-2-2V9m14 0H5M7 13h1m4 0h1m-5 4h1m4 0h1"/></svg>
                </x-slot>
                {{ __('Fasilitas') }}
            </x-sidebar-link>

            <x-sidebar-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </x-slot>
                {{ __('Keluhan') }}
            </x-sidebar-link>
        </nav>

        <p class="mt-6 px-3 pb-2 text-[11px] font-bold uppercase tracking-widest text-stone-400">Keuangan</p>
        <nav class="space-y-1">
            {{-- Accordion Keuangan --}}
            <div x-data="{ openFin: {{ request()->routeIs('finances.*') || request()->routeIs('payments.*') || request()->routeIs('admin.receipt.*') ? 'true' : 'false' }} }">
                <button @click="openFin = !openFin"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition focus:outline-none {{ request()->routeIs('finances.*') || request()->routeIs('payments.*') || request()->routeIs('admin.receipt.*') ? 'bg-brand-600 text-white shadow-soft' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span class="flex-1 text-start">{{ __('Keuangan') }}</span>
                    <svg class="h-4 w-4 transform transition-transform duration-200" :class="{ 'rotate-180': openFin }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="openFin" x-cloak class="space-y-1 pl-4">
                    <x-sidebar-link :href="route('finances.dashboard')" :active="request()->routeIs('finances.dashboard')">
                        <x-slot name="icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </x-slot>
                        Dashboard Keuangan
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('finances.index')" :active="request()->routeIs('finances.index')">
                        <x-slot name="icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </x-slot>
                        Pencatatan Keuangan
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('payments.index')" :active="request()->routeIs('payments.index')">
                        <x-slot name="icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </x-slot>
                        Pembayaran Sewa
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('finances.report')" :active="request()->routeIs('finances.report')">
                        <x-slot name="icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </x-slot>
                        Laporan Keuangan
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.receipt.create')" :active="request()->routeIs('admin.receipt.create')">
                        <x-slot name="icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </x-slot>
                        Buat Kwitansi Manual
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.receipt.history')" :active="request()->routeIs('admin.receipt.history')">
                        <x-slot name="icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </x-slot>
                        History Kwitansi
                    </x-sidebar-link>
                </div>
            </div>
        </nav>

        <p class="mt-6 px-3 pb-2 text-[11px] font-bold uppercase tracking-widest text-stone-400">Lainnya</p>
        <nav class="space-y-1">
            <x-sidebar-link :href="route('admin.logs')" :active="request()->routeIs('admin.logs')">
                <x-slot name="icon">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M9 8h6M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </x-slot>
                {{ __('Logs') }}
            </x-sidebar-link>

            {{-- Accordion Broadcast --}}
            <div x-data="{ openBroadcast: {{ request()->routeIs('broadcast.*') ? 'true' : 'false' }} }">
                <button @click="openBroadcast = !openBroadcast"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition focus:outline-none {{ request()->routeIs('broadcast.*') ? 'bg-brand-600 text-white shadow-soft' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.01 1.176L4.01 15.7a1.76 1.76 0 01-1.22-.55H2a2 2 0 01-2-2V10.82a2 2 0 012-2h.79a1.76 1.76 0 011.22-.55L7.99 3.66A1.76 1.76 0 0111 4.88zm8.27 6.88a6 6 0 010-5.53m3.53 9.8a10 10 0 010-14.3"/></svg>
                    <span class="flex-1 text-start">{{ __('Broadcast') }}</span>
                    <svg class="h-4 w-4 transform transition-transform duration-200" :class="{ 'rotate-180': openBroadcast }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="openBroadcast" x-cloak class="space-y-1 pl-4">
                    <x-sidebar-link :href="route('broadcast.index')" :active="request()->routeIs('broadcast.index')">
                        <x-slot name="icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </x-slot>
                        {{ __('Kirim Pesan') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('broadcast.history')" :active="request()->routeIs('broadcast.history')">
                        <x-slot name="icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </x-slot>
                        {{ __('Riwayat Broadcast') }}
                    </x-sidebar-link>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
                <x-sidebar-link :href="route('settings.backup.index')" :active="request()->routeIs('settings.backup.*')">
                    <x-slot name="icon">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </x-slot>
                    {{ __('Backup Database') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('settings.storage')" :active="request()->routeIs('settings.storage')">
                    <x-slot name="icon">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </x-slot>
                    {{ __('Pengaturan') }}
                </x-sidebar-link>
            @endif
        </nav>
    </div>

    <!-- Sidebar Footer: user card (mobile quick access) -->
    <div class="shrink-0 border-t border-stone-100 p-3">
        <div class="flex items-center gap-3 rounded-xl bg-stone-50/80 p-2.5">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-100 text-sm font-bold text-brand-800">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </span>
            <div class="min-w-0 flex-1">
                <div class="truncate text-sm font-bold text-stone-900">{{ Auth::user()->name }}</div>
                <div class="truncate text-xs text-stone-500">{{ Auth::user()->email }}</div>
            </div>
            <a href="{{ route('profile.edit') }}" class="rounded-lg p-2 text-stone-400 transition hover:bg-white hover:text-brand-600" title="{{ __('Profile') }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </a>
        </div>
    </div>
</aside>