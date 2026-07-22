<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Log Aktivitas &amp; Error</h2>
    </x-slot>

    <div class="py-6" x-data="{ tab: 'activity', selectedLog: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Stats --}}
            @php
                $total = $logs->total();
                $failed = $logs->filter(fn($l) => str_contains($l->action, 'FAILED'))->count();
                $today = $logs->filter(fn($l) => $l->created_at->isToday())->count();
            @endphp
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <div class="text-2xl font-black text-slate-800">{{ $total }}</div>
                    <div class="text-xs font-medium text-slate-400 mt-1">Total Log</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5">
                    <div class="text-2xl font-black text-red-600">{{ $failed }}</div>
                    <div class="text-xs font-medium text-red-400 mt-1">Error</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <div class="text-2xl font-black text-teal-600">{{ $today }}</div>
                    <div class="text-xs font-medium text-slate-400 mt-1">Hari Ini</div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="flex gap-1 bg-slate-100 p-1 rounded-2xl w-fit">
                <button @click="tab = 'activity'" :class="tab === 'activity' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2 rounded-xl text-sm font-bold transition">Activity Logs</button>
                <button @click="tab = 'laravel'" :class="tab === 'laravel' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2 rounded-xl text-sm font-bold transition">Laravel Log</button>
            </div>

            {{-- Tab: Activity Logs --}}
            <div x-show="tab === 'activity'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1">
                {{-- Filters --}}
                <form method="GET" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                    <div class="flex flex-wrap gap-3 items-end">
                        <div class="flex-1 min-w-[200px]">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cari Aksi</label>
                            <select name="action" class="mt-1 w-full rounded-xl border-slate-200 text-sm" onchange="this.form.submit()">
                                <option value="">Semua Aksi</option>
                                @foreach($actions as $a)
                                    <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.logs') }}?action=" class="px-4 py-2.5 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition">Reset</a>
                            <a href="{{ route('admin.logs') }}?action=FAILED" class="px-4 py-2.5 bg-red-50 text-red-600 text-sm font-bold rounded-xl hover:bg-red-100 transition">Error Saja</a>
                        </div>
                    </div>
                </form>

                {{-- Table --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="px-5 py-3">Waktu</th>
                                    <th class="px-5 py-3">User</th>
                                    <th class="px-5 py-3">Aksi</th>
                                    <th class="px-5 py-3 w-full">Deskripsi</th>
                                    <th class="px-5 py-3 text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    @php $isError = str_contains($log->action, 'FAILED'); @endphp
                                    <tr class="border-t border-slate-50 hover:bg-slate-50/50 transition cursor-pointer" @click="selectedLog = selectedLog === {{ $log->id }} ? null : {{ $log->id }}">
                                        <td class="px-5 py-3 whitespace-nowrap">
                                            <span class="font-mono text-xs text-slate-400">{{ $log->created_at->format('d M H:i') }}</span>
                                        </td>
                                        <td class="px-5 py-3">
                                            <span class="text-slate-700">{{ $log->user->name ?? 'Guest' }}</span>
                                        </td>
                                        <td class="px-5 py-3">
                                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold {{ $isError ? 'bg-red-100 text-red-700' : 'bg-teal-100 text-teal-700' }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-slate-600">{{ $log->description }}</td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="text-slate-300 text-xs" x-text="selectedLog === {{ $log->id }} ? '▲' : '▼'"></span>
                                        </td>
                                    </tr>
                                    <tr x-show="selectedLog === {{ $log->id }}" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0">
                                        <td colspan="5" class="px-5 py-4 bg-slate-50 border-t border-slate-100">
                                            <div class="grid grid-cols-2 gap-4 text-xs">
                                                <div>
                                                    <span class="font-bold text-slate-400 uppercase tracking-wider">Device</span>
                                                    <div class="mt-1 text-slate-700">
                                                        @if($log->payload && !empty($log->payload['device']))
                                                            <div><span class="text-slate-400">Browser:</span> {{ $log->payload['browser'] ?? '-' }}</div>
                                                            <div><span class="text-slate-400">OS:</span> {{ $log->payload['os'] ?? '-' }}</div>
                                                            <div><span class="text-slate-400">Tipe:</span> {{ $log->payload['device'] ?? '-' }}</div>
                                                            <div><span class="text-slate-400">IP:</span> {{ $log->payload['ip'] ?? $log->ip_address ?? '-' }}</div>
                                                        @else
                                                            <span class="text-slate-400">-</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="font-bold text-slate-400 uppercase tracking-wider">Error</span>
                                                    <div class="mt-1">
                                                        @if(!empty($log->payload['error_message']))
                                                            <div class="text-red-600 font-medium">{{ $log->payload['error_message'] }}</div>
                                                            @if(!empty($log->payload['error_file']))
                                                                <div class="text-slate-400 mt-0.5 font-mono text-[10px]">{{ $log->payload['error_file'] }}</div>
                                                            @endif
                                                        @else
                                                            <span class="text-slate-400">Tidak ada error</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if($log->user_agent)
                                                <div class="mt-3 pt-3 border-t border-slate-200 text-[10px] text-slate-400 font-mono break-all">{{ $log->user_agent }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">Belum ada log.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-4 border-t border-slate-100">
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

            {{-- Tab: Laravel Log --}}
            <div x-show="tab === 'laravel'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3 bg-slate-900 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-white">storage/logs/laravel.log</h3>
                        <span class="text-[10px] text-slate-500">100 baris terakhir</span>
                    </div>
                    <pre class="text-[11px] leading-relaxed font-mono text-slate-300 bg-slate-900 p-5 overflow-x-auto max-h-[600px] overflow-y-scroll">@if($laravelLog)@foreach($laravelLog as $line){{ $line }}@endforeach@else<code class="text-slate-500">File log tidak ditemukan.</code>@endif</pre>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
