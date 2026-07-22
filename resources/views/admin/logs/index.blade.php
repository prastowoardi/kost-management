<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Log Aktivitas & Error') }}
            </h2>
            <form method="GET" class="flex gap-2">
                <select name="action" class="rounded-xl border-slate-300 text-sm" onchange="this.form.submit()">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $a)
                        <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
                <a href="{{ route('admin.logs') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200">Reset</a>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Activity Logs --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-[2rem] border border-teal-50">
                <div class="p-4 bg-teal-50 border-b border-teal-100 rounded-t-[2rem]">
                    <h3 class="font-bold text-teal-800">Activity Logs</h3>
                </div>
                <div class="p-4 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-500">
                            <thead class="text-[10px] text-slate-600 uppercase bg-slate-50">
                                <tr>
                                    <th class="px-3 py-2">Waktu</th>
                                    <th class="px-3 py-2">User</th>
                                    <th class="px-3 py-2">Aksi</th>
                                    <th class="px-3 py-2">Deskripsi</th>
                                    <th class="px-3 py-2">Device</th>
                                    <th class="px-3 py-2">Pesan Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr class="border-b border-slate-100 {{ str_contains($log->action, 'FAILED') ? 'bg-red-50/50' : '' }}">
                                        <td class="px-3 py-2 text-slate-400 whitespace-nowrap font-mono">{{ $log->created_at->format('d M H:i') }}</td>
                                        <td class="px-3 py-2">{{ $log->user->name ?? 'Guest' }}</td>
                                        <td class="px-3 py-2">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ str_contains($log->action, 'FAILED') ? 'bg-red-100 text-red-700' : 'bg-teal-100 text-teal-700' }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-slate-700 max-w-xs truncate">{{ $log->description }}</td>
                                        <td class="px-3 py-2 text-slate-400 whitespace-nowrap">
                                            @if($log->payload && !empty($log->payload['device']))
                                                {{ $log->payload['browser'] ?? '-' }} / {{ $log->payload['os'] ?? '-' }} / {{ $log->payload['device'] ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-red-600 max-w-xs truncate">
                                            {{ $log->payload['error_message'] ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-8 text-center text-slate-400">Belum ada log.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

            {{-- Laravel Log --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-[2rem] border border-slate-200">
                <div class="p-4 bg-slate-800 text-white rounded-t-[2rem] flex justify-between">
                    <h3 class="font-bold">Laravel Log (100 baris terakhir)</h3>
                    <span class="text-[10px] text-slate-400">storage/logs/laravel.log</span>
                </div>
                <div class="p-0">
                    <pre class="text-[11px] leading-relaxed font-mono text-slate-300 bg-slate-900 p-4 overflow-x-auto max-h-96 overflow-y-scroll">@if($laravelLog)@foreach($laravelLog as $line){{ $line }}@endforeach@else<code class="text-slate-500">File log tidak ditemukan.</code>@endif</pre>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
