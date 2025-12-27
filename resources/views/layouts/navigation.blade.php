<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800">
                        🏠 Serrata Kos
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
                            <div class="py-1">
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
                            </div>
                        </div>
                    </div>
                    
                    <x-nav-link :href="route('facilities.index')" :active="request()->routeIs('facilities.*')">
                        {{ __('Fasilitas') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')">
                        {{ __('Keluhan') }}
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

                    @if(auth()->user()->isAdmin())
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        {{ __('Users') }}
                    </x-nav-link>
                    @endif
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
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
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
            
            <!-- Mobile Keuangan Group -->
            <div class="border-t border-b border-gray-200 py-2">
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Keuangan</div>
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
            </div>
            
            <x-responsive-nav-link :href="route('facilities.index')" :active="request()->routeIs('facilities.*')">
                {{ __('Fasilitas') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')">
                {{ __('Keluhan') }}
            </x-responsive-nav-link>
            
            <div class="pt-2 pb-1 border-t border-gray-200">
                <div class="px-4 font-medium text-base text-gray-600">{{ __('Broadcast WhatsApp') }}</div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('broadcast.index')" :active="request()->routeIs('broadcast.index')">
                        {{ __('Kirim Pesan') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('broadcast.history')" :active="request()->routeIs('broadcast.history')">
                        {{ __('Riwayat Broadcast') }}
                    </x-responsive-nav-link>
                </div>
            </div>
            
            @if(auth()->user()->isAdmin())
            <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                {{ __('Users') }}
            </x-responsive-nav-link>
            @endif
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