<header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-3 border-b border-stone-200/70 bg-white/90 px-4 backdrop-blur-lg sm:px-6">

    <!-- Sidebar toggle -->
    <button @click="sidebarOpen = !sidebarOpen"
        class="rounded-xl p-2 text-stone-500 transition hover:bg-stone-100 hover:text-stone-800 lg:hidden">
        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path :class="{ 'hidden': sidebarOpen, 'inline-flex': !sidebarOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{ 'hidden': !sidebarOpen, 'inline-flex': sidebarOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <div class="hidden lg:block">
        <p class="text-xs font-medium text-stone-400">Serrata Kost</p>
        <p class="-mt-0.5 text-sm font-extrabold text-stone-800">Back Office</p>
    </div>

    <div class="flex-1"></div>

    <!-- Notifikasi Bell -->
    <div class="flex items-center"
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
                class="fixed inset-x-3 top-16 z-50 flex flex-col overflow-hidden rounded-2xl bg-white shadow-lift ring-1 ring-stone-200/70 sm:absolute sm:inset-x-auto sm:right-0 sm:top-full sm:mt-2 sm:w-80"
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

    <!-- User Dropdown -->
    <div class="flex items-center">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center gap-2 rounded-xl py-1.5 pl-1.5 pr-3 text-sm font-semibold text-stone-700 transition hover:bg-stone-50 focus:outline-none">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-100 text-sm font-bold text-brand-700">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                    <span class="hidden max-w-[10rem] truncate sm:inline">{{ Auth::user()->name }}</span>
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
                    <x-dropdown-link :href="route('logout')" data-submit-closest-form>
                        <span class="flex items-center gap-2 text-red-600"><span>↪</span> {{ __('Log Out') }}</span>
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>