<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800">
                        🏠 Serrata Kost
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('rooms.index')" :active="request()->routeIs('rooms.*')">
                        {{ __('Kamar') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('tenants.index')" :active="request()->routeIs('tenants.*')">
                        {{ __('Penghuni') }}
                    </x-nav-link>
                    
                    <!-- Dropdown Menu: Keuangan -->
                    <div class="hidden sm:flex sm:items-center sm:ml-0 relative" x-data="{ openFinance: false }" @click.away="openFinance = false">
                        <button @click="openFinance = !openFinance" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out h-16
                                {{ request()->routeIs('payments.*') || request()->routeIs('finances.*') ? 'border-indigo-400 text-gray-900 focus:border-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }}">
                            <span>Keuangan</span>
                            <svg class="ml-1 h-4 w-4 transition-transform duration-200" :class="{'rotate-180': openFinance}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="openFinance" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 top-full mt-0 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                style="display: none;">
                            <div class="py-1 text-left">
                                <a href="{{ route('finances.dashboard') }}" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('finances.dashboard') ? 'bg-gray-50 font-semibold' : '' }}">
                                    <span class="mr-2">📊</span> Dashboard Keuangan
                                </a>
                                <a href="{{ route('finances.index') }}" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('finances.index') ? 'bg-gray-50 font-semibold' : '' }}">
                                    <span class="mr-2">💰</span> Pencatatan Keuangan
                                </a>
                                <a href="{{ route('payments.index') }}" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('payments.index') ? 'bg-gray-50 font-semibold' : '' }}">
                                    <span class="mr-2">💳</span> Pembayaran Sewa
                                </a>
                                <a href="{{ route('finances.report') }}" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('finances.report') ? 'bg-gray-50 font-semibold' : '' }}">
                                    <span class="mr-2">📄</span> Laporan Keuangan
                                </a>
                                <hr class="my-1 border-gray-100">
                                <a href="{{ route('admin.receipt.create') }}" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700 {{ request()->routeIs('admin.receipt.create') ? 'bg-teal-50 font-semibold text-teal-700' : '' }}">
                                    <span class="mr-2">📝</span> Buat Kwitansi Manual
                                </a>
                                <a href="{{ route('admin.receipt.history') }}" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700 {{ request()->routeIs('admin.receipt.history') ? 'bg-teal-50 font-semibold text-teal-700' : '' }}">
                                    <span class="mr-2">📜</span> History Kwitansi Manual
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <x-nav-link :href="route('facilities.index')" :active="request()->routeIs('facilities.*')">
                        {{ __('Fasilitas') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')">
                        {{ __('Keluhan') }}
                    </x-nav-link>

                    <x-nav-link :href="route('admin.logs')" :active="request()->routeIs('admin.logs')">
                        {{ __('Logs') }}
                    </x-nav-link>
                    
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 {{ request()->routeIs('broadcast.*') ? 'text-indigo-700 font-bold' : '' }}">
                                    <div>Broadcast WA</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
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
            <div class="hidden sm:flex sm:items-center sm:ml-4" 
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
                    <button @click="open = !open; if(open) fetchNotif()" class="relative p-2 text-gray-500 hover:text-gray-700 transition rounded-lg hover:bg-gray-100 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <template x-if="unread > 0">
                            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-500 rounded-full min-w-[18px] min-h-[18px]" x-text="unread"></span>
                        </template>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-80 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 border border-slate-100"
                        style="display: none;">
                        
                        <div class="p-3 border-b border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Notifikasi</span>
                            <template x-if="unread > 0">
                                <button @click="markAll()" class="text-[10px] font-bold text-blue-600 hover:text-blue-800">Tandai Dibaca</button>
                            </template>
                        </div>

                        <div class="max-h-80 overflow-y-auto">
                            <template x-if="items.length === 0">
                                <div class="p-6 text-center text-sm text-slate-400">Belum ada notifikasi</div>
                            </template>
                            <template x-for="n in items" :key="n.id">
                                <a :href="n.link || '#'" @click="markRead(n.id); open = false"
                                    class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 border-b border-slate-50 transition"
                                    :class="{ 'bg-blue-50/40': !n.is_read }">
                                    <div class="shrink-0 mt-0.5">
                                        <template x-if="n.type === 'keluhan_baru'">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600">!</span>
                                        </template>
                                        <template x-if="n.type === 'bayar_masuk'">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600">$</span>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate" x-text="n.title"></p>
                                        <p class="text-xs text-slate-500 truncate" x-text="n.message"></p>
                                        <p class="text-[10px] text-slate-400 mt-0.5" x-text="n.time"></p>
                                    </div>
                                    <template x-if="!n.is_read">
                                        <span class="shrink-0 w-2 h-2 mt-2 bg-blue-500 rounded-full"></span>
                                    </template>
                                </a>
                            </template>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Notif mobile --}}
            <div class="sm:hidden" 
                x-data="{ unreadM: 0 }"
                x-init="
                    fetch('{{ route('notifications.index') }}').then(r => r.json()).then(d => unreadM = d.unread_count);
                    setInterval(() => fetch('{{ route('notifications.index') }}').then(r => r.json()).then(d => unreadM = d.unread_count), 30000);
                ">
                <div class="flex items-center gap-2 pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600">
                    <span>🔔 Notifikasi</span>
                    <template x-if="unreadM > 0">
                        <span class="px-1.5 py-0.5 text-[10px] font-bold text-white bg-red-500 rounded-full" x-text="unreadM"></span>
                    </template>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-100 shadow-inner">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('rooms.index')" :active="request()->routeIs('rooms.*')">
                {{ __('Kamar') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('tenants.index')" :active="request()->routeIs('tenants.*')">
                {{ __('Penghuni') }}
            </x-responsive-nav-link>
            
            <div x-data="{ openFin: {{ request()->routeIs('finances.*') || request()->routeIs('payments.*') ? 'true' : 'false' }} }">
                <button @click="openFin = !openFin" class="w-full flex justify-between items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">
                    <span class="flex items-center">
                        {{ __('Keuangan') }}
                    </span>
                    <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': openFin }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openFin" x-cloak class="bg-gray-50 border-l-4 border-indigo-200">
                    <x-responsive-nav-link :href="route('finances.dashboard')" :active="request()->routeIs('finances.dashboard')" class="pl-8">
                        📊 Dashboard Keuangan
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('finances.index')" :active="request()->routeIs('finances.index')" class="pl-8">
                        💰 Pencatatan Keuangan
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.index')" class="pl-8">
                        💳 Pembayaran Sewa
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('finances.report')" :active="request()->routeIs('finances.report')" class="pl-8">
                        📄 Laporan Keuangan
                    </x-responsive-nav-link>
                    <div class="border-t border-gray-100 my-1"></div>
                    <x-responsive-nav-link :href="route('admin.receipt.create')" :active="request()->routeIs('admin.receipt.create')" class="pl-8 text-teal-600">
                        📝 Buat Kwitansi Manual
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.receipt.history')" :active="request()->routeIs('admin.receipt.history')" class="pl-8">
                        📜 History Kwitansi Manual
                    </x-responsive-nav-link>
                </div>
            </div>
            
            <x-responsive-nav-link :href="route('facilities.index')" :active="request()->routeIs('facilities.*')">
                {{ __('Fasilitas') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')">
                {{ __('Keluhan') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('admin.logs')" :active="request()->routeIs('admin.logs')">
                {{ __('Logs') }}
            </x-responsive-nav-link>
            
            <div x-data="{ openBroadcast: {{ request()->routeIs('broadcast.*') ? 'true' : 'false' }} }">
                <button @click="openBroadcast = !openBroadcast" class="w-full flex justify-between items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">
                    <span class="flex items-center">
                        {{ __('Broadcast WhatsApp') }}
                    </span>
                    <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': openBroadcast }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openBroadcast" x-cloak class="bg-gray-50 border-l-4 border-indigo-200">
                    <x-responsive-nav-link :href="route('broadcast.index')" :active="request()->routeIs('broadcast.index')" class="pl-8">
                        {{ __('Kirim Pesan') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('broadcast.history')" :active="request()->routeIs('broadcast.history')" class="pl-8">
                        {{ __('Riwayat Broadcast') }}
                    </x-responsive-nav-link>
                </div>
            </div>
            
            {{-- @if(auth()->user()->isAdmin())
            <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                {{ __('Users') }}
            </x-responsive-nav-link>
            @endif --}}
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
