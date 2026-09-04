<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="page-title">{{ __('Kalender Aktivitas') }}</h2>
                <p class="mt-1 text-sm text-stone-500">Jadwal check-in/out penghuni, pembayaran, dan keluhan.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('calendar', ['year' => $prev->year, 'month' => $prev->month]) }}" class="btn-secondary btn-sm !px-2.5" title="Bulan sebelumnya">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <span class="min-w-[170px] rounded-xl border border-stone-200 bg-white px-4 py-2 text-center text-sm font-bold text-stone-800 shadow-sm">
                    {{ $current->translatedFormat('F Y') }}
                </span>
                <a href="{{ route('calendar', ['year' => $next->year, 'month' => $next->month]) }}" class="btn-secondary btn-sm !px-2.5" title="Bulan berikutnya">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('calendar') }}" class="btn-secondary btn-sm">
                    Hari Ini
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10"
        x-data="{
            selectedDay: null,
            dayEvents: null,
            showDay(day, events) {
                this.selectedDay = day;
                this.dayEvents = events;
            }
        }">
        <div class="page-container">

            @php
                $statCards = [
                    'move-in'   => ['label' => 'Penghuni Masuk', 'color' => 'emerald', 'icon' => 'arrow-down-tray'],
                    'move-out'  => ['label' => 'Penghuni Keluar', 'color' => 'rose', 'icon' => 'arrow-up-tray'],
                    'payment'   => ['label' => 'Pembayaran', 'color' => 'sky', 'icon' => 'banknotes'],
                    'complaint' => ['label' => 'Keluhan', 'color' => 'orange', 'icon' => 'megaphone'],
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                @foreach($statCards as $key => $card)
                    <div class="rounded-2xl border border-stone-200/80 bg-white p-4 shadow-soft">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                                @if($card['color'] == 'emerald') bg-emerald-100 text-emerald-700
                                @elseif($card['color'] == 'rose') bg-rose-100 text-rose-700
                                @elseif($card['color'] == 'sky') bg-sky-100 text-sky-700
                                @else bg-orange-100 text-orange-700 @endif">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($card['icon'] == 'arrow-down-tray')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    @elseif($card['icon'] == 'arrow-up-tray')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10l-7-7m0 0l-7 7m7-7v18"/>
                                    @elseif($card['icon'] == 'banknotes')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.01 1.176L4.01 15.7a1.76 1.76 0 01-1.22-.55H2a2 2 0 01-2-2V10.82a2 2 0 012-2h.79a1.76 1.76 0 011.22-.55L7.99 3.66A1.76 1.76 0 0111 4.88zm8.27 6.88a6 6 0 010-5.53m3.53 9.8a10 10 0 010-14.3"/>
                                    @endif
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold leading-none text-stone-900">{{ $counts[$key] }}</p>
                                <p class="mt-1 text-xs font-semibold text-stone-500">{{ $card['label'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">

                {{-- CALENDAR --}}
                <div class="card overflow-hidden">
                    <div class="card-body">

                        <div class="overflow-x-auto">
                            <div class="min-w-[680px]">
                                <table class="w-full table-fixed border-separate" style="border-spacing: 4px;">
                                <thead>
                                    <tr>
                                        @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $idx => $day)
                                            <th class="px-2 py-2 text-center text-xs font-bold uppercase tracking-wider
                                                {{ $idx >= 5 ? 'text-rose-400' : 'text-stone-500' }}">
                                                {{ $day }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($weeks as $week)
                                    <tr>
                                        @foreach($week as $cell)
                                        <td class="align-top text-left">
                                            <div x-data
                                                class="group relative flex min-h-[108px] cursor-pointer flex-col rounded-xl border p-1.5 transition
                                                {{ $cell['inMonth'] ? 'bg-white' : 'bg-stone-50/60' }}
                                                @if($cell['isToday']) border-brand-400 shadow-soft ring-2 ring-brand-200
                                                @else border-stone-200 hover:border-brand-300 hover:shadow-soft @endif"
                                                @click="showDay({
                                                        key: '{{ $cell['date']->format('Y-m-d') }}',
                                                        label: '{{ $cell['date']->translatedFormat('l, d F Y') }}'
                                                    },
                                                    @json($cell['events']))">

                                                <div class="flex items-start justify-between px-0.5 pb-1">
                                                    <span class="ml-auto inline-flex h-6 min-w-6 items-center justify-center rounded-full px-1.5 text-[11px] font-bold
                                                        @if($cell['isToday']) bg-brand-600 text-white
                                                        @elseif($cell['inMonth']) text-stone-600 group-hover:text-stone-800
                                                        @else text-stone-300 @endif">
                                                        {{ $cell['day'] }}
                                                    </span>
                                                </div>

                                                <div class="space-y-1">
                                                    @foreach(array_slice($cell['events'], 0, 3) as $event)
                                                        <a href="{{ $event['url'] }}"
                                                            class="flex items-center gap-1 truncate rounded-md px-1 py-0.5 text-[10px] font-semibold leading-tight transition
                                                            @if($event['color'] == 'emerald') bg-emerald-100 text-emerald-800 hover:bg-emerald-200
                                                            @elseif($event['color'] == 'rose') bg-rose-100 text-rose-700 hover:bg-rose-200
                                                            @elseif($event['color'] == 'sky') bg-sky-100 text-sky-800 hover:bg-sky-200
                                                            @elseif($event['color'] == 'amber') bg-amber-100 text-amber-800 hover:bg-amber-200
                                                            @elseif($event['color'] == 'violet') bg-violet-100 text-violet-800 hover:bg-violet-200
                                                            @elseif($event['color'] == 'teal') bg-teal-100 text-teal-800 hover:bg-teal-200
                                                            @else bg-orange-100 text-orange-800 hover:bg-orange-200 @endif"
                                                            title="{{ $event['label'] }} · {{ $event['sub'] }}"
                                                            @click.stop>
                                                            <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                @if($event['icon'] == 'arrow-down-tray')
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                                                @elseif($event['icon'] == 'arrow-up-tray')
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10l-7-7m0 0l-7 7m7-7v18"/>
                                                                @elseif($event['icon'] == 'banknotes')
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                                @elseif($event['icon'] == 'megaphone')
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.01 1.176L4.01 15.7a1.76 1.76 0 01-1.22-.55H2a2 2 0 01-2-2V10.82a2 2 0 012-2h.79a1.76 1.76 0 011.22-.55L7.99 3.66A1.76 1.76 0 0111 4.88zm8.27 6.88a6 6 0 010-5.53m3.53 9.8a10 10 0 010-14.3"/>
                                                                @else
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                @endif
                                                            </svg>
                                                            <span class="min-w-0 flex-1 truncate">{{ $event['label'] }}</span>
                                                        </a>
                                                    @endforeach

                                                    @if(count($cell['events']) > 3)
                                                        <span class="block truncate rounded-md px-1 py-0.5 text-[10px] font-semibold text-stone-400">
                                                            +{{ count($cell['events']) - 3 }} lainnya
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-stone-100 pt-4">
                            <div class="flex flex-wrap items-center gap-4 text-xs text-stone-500">
                                <span class="flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Masuk
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span> Keluar
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-sky-500"></span> Pembayaran
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-orange-500"></span> Keluhan
                                </span>
                            </div>
                            <span class="text-xs text-stone-400">Klik tanggal untuk melihat detail</span>
                        </div>

                    </div>
                </div>

                {{-- SIDEBAR --}}
                <div class="space-y-6">

                    <div class="card overflow-hidden">
                        <div class="card-header bg-gradient-to-r from-brand-600 to-brand-500">
                            <h3 class="section-title !text-white flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Agenda Bulan Ini
                            </h3>
                        </div>
                        <div class="card-body">
                            @if(count($upcoming) === 0)
                                <div class="empty-state !py-8">
                                    <p class="text-sm font-medium text-stone-600">Tidak ada aktivitas</p>
                                    <p class="mt-1 text-xs text-stone-400">Tidak ada agenda pada bulan ini.</p>
                                </div>
                            @else
                                <ol class="relative space-y-4 border-l-2 border-stone-100 pl-4 ml-2">
                                    @foreach($upcoming as $item)
                                        <li class="relative">
                                            <span class="absolute -left-[23px] top-1 flex h-4 w-4 items-center justify-center rounded-full ring-4 ring-white
                                                @if($item['color'] == 'emerald') bg-emerald-500
                                                @elseif($item['color'] == 'rose') bg-rose-500
                                                @elseif($item['color'] == 'sky') bg-sky-500
                                                @elseif($item['color'] == 'amber') bg-amber-500
                                                @elseif($item['color'] == 'violet') bg-violet-500
                                                @else bg-orange-500 @endif">
                                            </span>
                                            <div class="flex items-start justify-between gap-2">
                                                <a href="{{ $item['url'] }}" class="group min-w-0">
                                                    <p class="truncate text-sm font-semibold text-stone-800 group-hover:text-brand-600">{{ $item['title'] }}</p>
                                                    <p class="mt-0.5 truncate text-xs text-stone-500">{{ $item['sub'] }}</p>
                                                </a>
                                                <span class="shrink-0 text-xs font-bold text-stone-400">{{ $item['date']->format('d M') }}</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </div>
                    </div>

                    {{-- Detail Hari --}}
                    <div class="card overflow-hidden">
                        <div class="card-header bg-stone-50/60">
                            <h3 class="section-title flex items-center gap-2">
                                <svg class="h-4 w-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Detail Hari
                            </h3>
                        </div>
                        <div class="card-body">
                            <template x-if="!selectedDay">
                                <p class="py-6 text-center text-sm text-stone-400">Klik salah satu tanggal pada kalender untuk melihat detailnya.</p>
                            </template>
                            <template x-if="selectedDay">
                                <div>
                                    <p class="mb-3 font-bold text-stone-800" x-text="selectedDay.label"></p>

                                    <div class="space-y-2">
                                        <template x-for="event in (dayEvents || [])" :key="event.url + event.label">
                                            <a :href="event.url"
                                                class="flex items-center gap-2 rounded-lg border border-stone-200 bg-white p-2.5 transition hover:border-brand-300 hover:shadow-soft">
                                                <span class="h-8 w-1.5 shrink-0 rounded-full"
                                                    :class="{
                                                        'bg-emerald-500': event.color === 'emerald',
                                                        'bg-rose-500': event.color === 'rose',
                                                        'bg-sky-500': event.color === 'sky',
                                                        'bg-amber-500': event.color === 'amber',
                                                        'bg-violet-500': event.color === 'violet',
                                                        'bg-teal-500': event.color === 'teal',
                                                        'bg-orange-500': event.color === 'orange'
                                                    }"></span>
                                                <div class="min-w-0">
                                                    <p class="truncate text-sm font-semibold text-stone-800" x-text="event.label"></p>
                                                    <p class="truncate text-xs text-stone-500" x-text="event.sub"></p>
                                                </div>
                                            </a>
                                        </template>
                                        <template x-if="!dayEvents || dayEvents.length === 0">
                                            <p class="py-4 text-center text-sm text-stone-400">Tidak ada aktivitas pada tanggal ini.</p>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>