<nav class="sticky top-0 z-40 border-b border-stone-200/70 bg-white/90 backdrop-blur-lg">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <!-- Logo -->
                <div class="flex shrink-0 items-center">
                    <a href="{{ route('dashboard') }}" class="group flex items-center gap-2.5">
                        <img src="{{ asset('serrata.png') }}" alt="Serrata Kost" class="h-9 w-9 rounded-xl object-cover shadow-soft transition-transform duration-200 group-hover:scale-105">
                        <span class="text-lg font-extrabold tracking-tight text-stone-900">
                            Serrata <span class="text-brand-600">Kost</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 lg:-my-px lg:ml-8 lg:flex lg:items-stretch">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('rooms.index')" :active="request()->routeIs('rooms.*')">
                        {{ __('Kamar') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('tenants.index')" :active="request()->routeIs('tenants.*')">
                        {{ __('Penghuni') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('facilities.index')" :active="request()->routeIs('facilities.*')">
                        {{ __('Fasilitas') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')">
                        {{ __('Keluhan') }}
                    </x-nav-link>

                    <!-- Dropdown Menu: Keuangan -->
                    <div class="relative flex items-center" x-data="{ openFinance: false }" @click.away="openFinance = false">
                        <button @click="openFinance = !openFinance" 
                                class="inline-flex h-full items-center border-b-2 px-3 text-sm font-semibold leading-5 transition duration-150 ease-in-out focus:outline-none
                                {{ request()->routeIs('payments.*') || request()->routeIs('finances.*') ? 'border-brand-600 text-brand-700' : 'border-transparent text-stone-500 hover:border-stone-300 hover:text-stone-800' }}">
                            <span>Keuangan</span>
                            <svg class="ml-1 h-4 w-4 transition-transform duration-200" :class="{'rotate-180': openFinance}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="openFinance" 
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 top-full mt-1 w-64 rounded-2xl border border-stone-100 bg-white p-1.5 shadow-lift z-50"
                                style="display: none;">
                            <a href="{{ route('finances.dashboard') }}" 
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-stone-700 transition hover:bg-stone-50 {{ request()->routeIs('finances.dashboard') ? 'bg-brand-50 font-semibold text-brand-700' : '' }}">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-base">📊</span> Dashboard Keuangan
                            </a>
                            <a href="{{ route('finances.index') }}" 
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-stone-700 transition hover:bg-stone-50 {{ request()->routeIs('finances.index') ? 'bg-brand-50 font-semibold text-brand-700' : '' }}">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-base">💰</span> Pencatatan Keuangan
                            </a>
                            <a href="{{ route('payments.index') }}" 
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-stone-700 transition hover:bg-stone-50 {{ request()->routeIs('payments.index') ? 'bg-brand-50 font-semibold text-brand-700' : '' }}">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-base">💳</span> Pembayaran Sewa
                            </a>
                            <a href="{{ route('finances.report') }}" 
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-stone-700 transition hover:bg-stone-50 {{ request()->routeIs('finances.report') ? 'bg-brand-50 font-semibold text-brand-700' : '' }}">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-base">📄</span> Laporan Keuangan
                            </a>
                            <div class="my-1 border-t border-stone-100"></div>
                            <a href="{{ route('admin.receipt.create') }}" 
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-stone-700 transition hover:bg-stone-50 {{ request()->routeIs('admin.receipt.create') ? 'bg-brand-50 font-semibold text-brand-700' : '' }}">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-base">📝</span> Buat Kwitansi Manual
                            </a>
                            <a href="{{ route('admin.receipt.history') }}" 
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-stone-700 transition hover:bg-stone-50 {{ request()->routeIs('admin.receipt.history') ? 'bg-brand-50 font-semibold text-brand-700' : '' }}">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-base">📜</span> History Kwitansi
                            </a>
                        </div>
                    </div>

                    <x-nav-link :href="route('admin.logs')" :active="request()->routeIs('admin.logs')">
                        {{ __('Logs') }}
                    </x-nav-link>
                    
                    <div class="flex items-center sm:ms-2">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center rounded-xl px-3 py-2 text-sm font-medium text-stone-500 transition hover:bg-stone-50 hover:text-stone-800 focus:outline-none {{ request()->routeIs('broadcast.*') ? 'bg-brand-50 font-semibold text-brand-700' : '' }}">
                                    <span class="ml-1.5 hidden lg:inline">Broadcast</span>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('broadcast.index')">
                                    {{ __('Kirim Pesan') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('broadcast.history')">
                                    {{ __('Riwayat Broadcast') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    {{-- @if(auth()->user()->isAdmin())
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        {{ __('Users') }}
                    </x-nav-link>
                    @endif --}}
                </div>
            </div>

            <!-- Notifikasi Bell -->
            <div class="flex items-center sm:ml-1" 
                x-data="{ 
                    open: false, 
                    items: [], 
                    unread: 0,
                    async fetchNotif() {
                        const r = await fetch('{{ route('notifications.index') }}');
                        const d = await r.json();
                        this.items = d.items;
                        this.unread = d.unread_count;
                    },
                    async markRead(id) {
                        await fetch('{{ url('notifications') }}/' + id + '/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                        this.unread = Math.max(0, this.unread - 1);
                        this.items = this.items.map(n => n.id === id ? { ...n, is_read: true } : n);
                    },
                    async markAll() {
                        await fetch('{{ route('notifications.readAll') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                        this.unread = 0;
                        this.items = this.items.map(n => ({ ...n, is_read: true }));
                    }
                }"
                x-init="fetchNotif(); setInterval(() => fetchNotif(), 30000)">
                <div class="relative">
                    <button @click="open = !open; if(open) fetchNotif()" class="relative rounded-xl p-2 text-stone-500 transition hover:bg-stone-100 hover:text-stone-800 focus:outline-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <template x-if="unread > 0">
                            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-500 rounded-full min-w-[18px] min-h-[18px]" x-text="unread"></span>
                        </template>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="fixed inset-x-3 top-16 z-50 overflow-hidden rounded-2xl bg-white shadow-lift ring-1 ring-stone-200/70 sm:absolute sm:inset-x-auto sm:right-0 sm:top-full sm:mt-2 sm:w-80"
                        style="display: none;">
                        
                        <div class="flex items-center justify-between border-b border-stone-100 p-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-stone-500">Notifikasi</span>
                            <template x-if="unread > 0">
                                <button @click="markAll()" class="text-[10px] font-bold text-brand-600 hover:text-brand-800">Tandai Dibaca</button>
                            </template>
                        </div>

                        <div class="max-h-[70vh] overflow-y-auto sm:max-h-80">
                            <template x-if="items.length === 0">
                                <div class="p-6 text-center text-sm text-stone-400">Belum ada notifikasi</div>
                            </template>
                            <template x-for="n in items" :key="n.id">
                                <a :href="n.link || '#'" @click="markRead(n.id); open = false"
                                    class="flex items-start gap-3 border-b border-stone-50 px-4 py-3 transition hover:bg-stone-50"
                                    :class="{ 'bg-brand-50/40': !n.is_read }">
                                    <div class="shrink-0 mt-0.5">
                                        <template x-if="n.type === 'keluhan_baru'">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600">!</span>
                                        </template>
                                        <template x-if="n.type === 'bayar_masuk'">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600">$</span>
                                        </template>
                                        <template x-if="n.type === 'complaint_update'">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-600">!</span>
                                        </template>
                                        <template x-if="n.type === 'payment_verified'">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-brand-100 text-brand-600">✓</span>
                                        </template>
                                        <template x-if="n.type === 'payment_rejected'">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600">✕</span>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm truncate" x-text="n.title"
                                            :class="{ 'font-bold text-stone-900': !n.is_read, 'font-medium text-stone-500': n.is_read }"></p>
                                        <p class="text-xs text-stone-500 truncate" x-text="n.message"></p>
                                        <p class="text-[10px] text-stone-400 mt-0.5" x-text="n.time"></p>
                                    </div>
                                    <div class="shrink-0 flex flex-col items-end gap-1">
                                        <template x-if="!n.is_read">
                                            <span class="text-[9px] font-bold text-white bg-brand-600 px-1.5 py-0.5 rounded-full uppercase leading-tight">Baru</span>
                                        </template>
                                        <template x-if="n.is_read">
                                            <span class="text-[9px] font-medium text-stone-400 px-1.5 py-0.5 uppercase leading-tight">Dibaca</span>
                                        </template>
                                    </div>
                                </a>
                            </template>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-2">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 rounded-xl py-1.5 pl-1.5 pr-3 text-sm font-semibold text-stone-700 transition hover:bg-stone-50 focus:outline-none">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-100 text-sm font-bold text-brand-700">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="max-w-[10rem] truncate">{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4 text-stone-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            <span class="flex items-center gap-2"><span>👤</span> {{ __('Profile') }}</span>
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    data-submit-closest-form>
                                <span class="flex items-center gap-2 text-red-600"><span>↪</span> {{ __('Log Out') }}</span>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center lg:hidden">
                <button @click="menuOpen = ! menuOpen" class="inline-flex items-center justify-center rounded-xl p-2 text-stone-500 transition hover:bg-stone-100 hover:text-stone-700 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': menuOpen, 'inline-flex': ! menuOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! menuOpen, 'inline-flex': menuOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

</nav>

<!-- Mobile Menu Drawer -->
<div x-cloak x-show="menuOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[60] bg-stone-900/50 backdrop-blur-sm lg:hidden" @click="menuOpen = false"></div>

<div x-cloak x-show="menuOpen" @click.away="menuOpen = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-x-full opacity-0"
    x-transition:enter-end="translate-x-0 opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-x-0 opacity-100"
    x-transition:leave-end="translate-x-full opacity-0"
    class="fixed inset-y-0 right-0 z-[70] flex w-[85%] max-w-sm flex-col overflow-y-auto bg-white shadow-lift lg:hidden">
    <div class="sticky top-0 z-10 flex items-center gap-3 border-b border-stone-100 bg-white/95 px-5 py-4 backdrop-blur">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-base font-bold text-brand-800 ring-1 ring-brand-200">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </span>
        <div class="min-w-0 flex-1">
            <div class="truncate font-bold text-stone-900">{{ Auth::user()->name }}</div>
            <div class="truncate text-xs text-stone-500">{{ Auth::user()->email }}</div>
        </div>
        <button @click="menuOpen = false" class="rounded-xl p-2 text-stone-400 transition hover:bg-stone-100 hover:text-stone-700">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div class="flex-1 px-4 py-5">
        <p class="px-1 pb-2 text-[11px] font-bold uppercase tracking-widest text-stone-400">Menu Utama</p>
        <div class="space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('rooms.index')" :active="request()->routeIs('rooms.*')">
                {{ __('Kamar') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tenants.index')" :active="request()->routeIs('tenants.*')">
                {{ __('Penghuni') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('facilities.index')" :active="request()->routeIs('facilities.*')">
                {{ __('Fasilitas') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')">
                {{ __('Keluhan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.logs')" :active="request()->routeIs('admin.logs')">
                {{ __('Logs') }}
            </x-responsive-nav-link>
        </div>

        <p class="mt-5 px-1 pb-2 text-[11px] font-bold uppercase tracking-widest text-stone-400">Keuangan</p>
        <div x-data="{ openFin: {{ request()->routeIs('finances.*') || request()->routeIs('payments.*') || request()->routeIs('admin.receipt.*') ? 'true' : 'false' }} }" class="space-y-1">
            <button @click="openFin = !openFin"
                class="flex w-full items-center justify-between rounded-xl px-4 py-2.5 text-sm font-semibold transition hover:bg-stone-50 focus:outline-none {{ request()->routeIs('finances.*') || request()->routeIs('payments.*') || request()->routeIs('admin.receipt.*') ? 'text-brand-700' : 'text-stone-700' }}">
                <span>{{ __('Keuangan') }}</span>
                <svg class="h-4 w-4 transform text-stone-400 transition-transform" :class="{ 'rotate-180 text-brand-500': openFin }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="openFin" x-cloak x-transition class="space-y-1 border-l border-stone-200 pl-3">
                <x-responsive-nav-link :href="route('finances.dashboard')" :active="request()->routeIs('finances.dashboard')">
                    📊 Dashboard Keuangan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('finances.index')" :active="request()->routeIs('finances.index')">
                    💰 Pencatatan Keuangan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.index')">
                    💳 Pembayaran Sewa
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('finances.report')" :active="request()->routeIs('finances.report')">
                    📄 Laporan Keuangan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.receipt.create')" :active="request()->routeIs('admin.receipt.create')">
                    📝 Buat Kwitansi Manual
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.receipt.history')" :active="request()->routeIs('admin.receipt.history')">
                    📜 History Kwitansi Manual
                </x-responsive-nav-link>
            </div>
        </div>

        <p class="mt-5 px-1 pb-2 text-[11px] font-bold uppercase tracking-widest text-stone-400">Lainnya</p>
        <div x-data="{ openBroadcast: {{ request()->routeIs('broadcast.*') ? 'true' : 'false' }} }" class="space-y-1">
            <button @click="openBroadcast = !openBroadcast"
                class="flex w-full items-center justify-between rounded-xl px-4 py-2.5 text-sm font-semibold transition hover:bg-stone-50 focus:outline-none {{ request()->routeIs('broadcast.*') ? 'text-brand-700' : 'text-stone-700' }}">
                <span>{{ __('Broadcast WhatsApp') }}</span>
                <svg class="h-4 w-4 transform text-stone-400 transition-transform" :class="{ 'rotate-180 text-brand-500': openBroadcast }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="openBroadcast" x-cloak x-transition class="space-y-1 border-l border-stone-200 pl-3">
                <x-responsive-nav-link :href="route('broadcast.index')" :active="request()->routeIs('broadcast.index')">
                    {{ __('Kirim Pesan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('broadcast.history')" :active="request()->routeIs('broadcast.history')">
                    {{ __('Riwayat Broadcast') }}
                </x-responsive-nav-link>
            </div>
        </div>
    </div>

    <div class="border-t border-stone-200/70 bg-stone-50/60 px-4 py-4">
        <div class="space-y-1">
            <x-responsive-nav-link :href="route('profile.edit')">
                👤 {{ __('Profile') }}
            </x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                        data-submit-closest-form>
                    <span class="text-red-600">↪ {{ __('Log Out') }}</span>
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</div>
