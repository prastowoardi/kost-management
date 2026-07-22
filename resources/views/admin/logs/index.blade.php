@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
.select2-container--default .select2-selection--single {
    border: 1px solid #e2e8f0 !important;
    border-radius: 0.75rem !important;
    height: 42px !important;
    padding: 0 12px;
    background: #fff !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px !important;
    font-size: 14px;
    color: #334155 !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px !important;
    right: 8px !important;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #94a3b8 !important;
}
.select2-dropdown {
    border: 1px solid #e2e8f0 !important;
    border-radius: 0.75rem !important;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}
.select2-search--dropdown {
    padding: 8px;
}
.select2-search__field {
    border-radius: 0.5rem !important;
    border: 1px solid #e2e8f0 !important;
    padding: 6px 10px !important;
    font-size: 13px !important;
}
.select2-results__option {
    padding: 8px 12px !important;
    font-size: 13px;
}
.select2-results__option--highlighted {
    background-color: #f1f5f9 !important;
    color: #1e293b !important;
}
.select2-container--default .select2-results__option--selected {
    background-color: #f0fdf4 !important;
    color: #166534 !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
jQuery(function() {
    jQuery('.select2-action').select2({
        placeholder: 'Cari aksi...',
        allowClear: true,
        width: '100%'
    }).on('change', function() {
        var val = jQuery(this).val();
        if (!val) {
            window.location.href = '{{ route('admin.logs') }}';
        } else {
            window.location.href = '{{ route('admin.logs') }}?action=' + encodeURIComponent(val);
        }
    });
});
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Log Aktivitas</h2>
    </x-slot>

    <div class="py-4 sm:py-6" x-data="{ selectedLog: null }">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-5">

            {{-- Stats --}}
            @php
                $total = $logs->total();
                $failed = $logs->filter(fn($l) => str_contains($l->action, 'FAILED'))->count();
                $today = $logs->filter(fn($l) => $l->created_at->isToday())->count();
            @endphp
            <div class="grid grid-cols-3 gap-2 sm:gap-4">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 p-3 sm:p-5">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-teal-50 flex items-center justify-center text-teal-600">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-lg sm:text-2xl font-black text-slate-800">{{ $total }}</div>
                            <div class="text-[10px] sm:text-xs font-medium text-slate-400 truncate">Total Log</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-red-100 p-3 sm:p-5">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-red-50 flex items-center justify-center text-red-500">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-lg sm:text-2xl font-black text-red-600">{{ $failed }}</div>
                            <div class="text-[10px] sm:text-xs font-medium text-red-400 truncate">Error</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 p-3 sm:p-5">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-lg sm:text-2xl font-black text-blue-600">{{ $today }}</div>
                            <div class="text-[10px] sm:text-xs font-medium text-slate-400 truncate">Hari Ini</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 p-3 sm:p-4">
                <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                    <div class="flex-1 min-w-0">
                        <select class="select2-action">
                            <option value="">Semua Aksi</option>
                            @foreach($actions as $a)
                                <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <a href="{{ route('admin.logs') }}" class="flex-1 sm:flex-none text-center px-4 py-2.5 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition {{ !request('action') ? 'ring-2 ring-slate-300' : '' }}">Semua</a>
                        <a href="{{ route('admin.logs') }}?action=FAILED" class="flex-1 sm:flex-none text-center px-4 py-2.5 text-sm font-bold rounded-xl transition {{ request('action') == 'FAILED' ? 'bg-red-600 text-white shadow-md' : 'bg-red-50 text-red-600 hover:bg-red-100' }}">Error</a>
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="space-y-2 sm:space-y-2">
                @forelse($logs as $log)
                    @php $isError = str_contains($log->action, 'FAILED'); @endphp
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border {{ $isError ? 'border-red-200' : 'border-slate-100' }} overflow-hidden transition hover:shadow-md">
                        {{-- Baris utama --}}
                        <div class="flex items-start sm:items-center gap-2 sm:gap-4 px-3 sm:px-5 py-3 sm:py-4 cursor-pointer select-none" @click="selectedLog = selectedLog === {{ $log->id }} ? null : {{ $log->id }}">
                            {{-- Icon --}}
                            <div class="shrink-0 mt-0.5 sm:mt-0">
                                <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl flex items-center justify-center {{ $isError ? 'bg-red-100 text-red-600' : 'bg-teal-100 text-teal-600' }}">
                                    {!! $isError ? '<svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>' : '<svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' !!}
                                </div>
                            </div>
                            {{-- Info tengah --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    {{-- Aksi badge --}}
                                    <span class="inline-block px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-[11px] font-bold leading-tight {{ $isError ? 'bg-red-100 text-red-700' : 'bg-teal-100 text-teal-700' }}">
                                        {{ $log->action }}
                                    </span>
                                    {{-- User --}}
                                    <span class="text-[11px] sm:text-sm text-slate-400">{{ $log->user->name ?? 'Guest' }}</span>
                                </div>
                                {{-- Deskripsi --}}
                                <div class="text-xs sm:text-sm text-slate-700 mt-0.5 line-clamp-2 sm:line-clamp-1">{{ $log->description }}</div>
                            </div>
                            {{-- Waktu + Expand --}}
                            <div class="shrink-0 text-right">
                                <div class="text-[10px] sm:text-xs font-mono font-bold {{ $isError ? 'text-red-500' : 'text-slate-400' }}">{{ $log->created_at->format('H:i') }}</div>
                                <div class="text-[9px] sm:text-[10px] text-slate-400">{{ $log->created_at->format('d/m') }}</div>
                                <div class="mt-1 text-slate-300 transition" :class="{ 'rotate-180': selectedLog === {{ $log->id }} }">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                        {{-- Detail --}}
                        <div x-show="selectedLog === {{ $log->id }}" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0">
                            <div class="mx-3 sm:mx-5 mb-3 sm:mb-4 p-3 sm:p-4 rounded-xl {{ $isError ? 'bg-red-50/50 border border-red-100' : 'bg-slate-50 border border-slate-100' }}">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 text-xs">
                                    {{-- Device --}}
                                    <div>
                                        <div class="font-bold text-slate-400 uppercase tracking-wider text-[10px] mb-1">Device</div>
                                        @if($log->payload && !empty($log->payload['device']))
                                            <div class="space-y-0.5">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-10 sm:w-12 text-slate-400 shrink-0">Browser</span>
                                                    <span class="font-medium text-slate-700">{{ $log->payload['browser'] ?? '-' }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-10 sm:w-12 text-slate-400 shrink-0">OS</span>
                                                    <span class="font-medium text-slate-700">{{ $log->payload['os'] ?? '-' }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-10 sm:w-12 text-slate-400 shrink-0">Tipe</span>
                                                    <span class="font-medium text-slate-700">{{ $log->payload['device'] ?? '-' }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-10 sm:w-12 text-slate-400 shrink-0">IP</span>
                                                    <span class="font-medium text-slate-700">{{ $log->payload['ip'] ?? $log->ip_address ?? '-' }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </div>
                                    {{-- Error --}}
                                    <div>
                                        <div class="font-bold text-slate-400 uppercase tracking-wider text-[10px] mb-1">Error</div>
                                        @if(!empty($log->payload['error_message']))
                                            <div class="text-red-600 font-medium text-xs sm:text-sm">{{ $log->payload['error_message'] }}</div>
                                            @if(!empty($log->payload['error_file']))
                                                <div class="text-slate-400 mt-1 font-mono text-[10px] break-all">{{ $log->payload['error_file'] }}</div>
                                            @endif
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </div>
                                    {{-- Payload --}}
                                    <div>
                                        <div class="font-bold text-slate-400 uppercase tracking-wider text-[10px] mb-1">Data Tambahan</div>
                                        @php
                                            $extra = collect($log->payload ?? [])->except(['browser','os','device','ip','error_message','error_file'])->toArray();
                                        @endphp
                                        @if(!empty($extra))
                                            <div class="space-y-0.5">
                                                @foreach($extra as $k => $v)
                                                    <div class="flex items-start gap-1">
                                                        <span class="shrink-0 text-slate-400 text-[10px]">{{ $k }}:</span>
                                                        <span class="text-slate-700 break-all text-[10px]">{{ is_array($v) ? json_encode($v) : $v }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </div>
                                </div>
                                @if($log->user_agent)
                                    <div class="mt-2 sm:mt-3 pt-2 sm:pt-3 border-t {{ $isError ? 'border-red-200' : 'border-slate-200' }}">
                                        <div class="font-bold text-slate-400 uppercase tracking-wider text-[10px] mb-1">User Agent</div>
                                        <div class="text-[9px] sm:text-[10px] text-slate-400 font-mono break-all">{{ $log->user_agent }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 p-8 sm:p-12 text-center">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto rounded-xl sm:rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300 mb-3 sm:mb-4">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="text-sm sm:text-base text-slate-400 font-medium">Belum ada log aktivitas</div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 px-3 sm:px-5 py-3 sm:py-4">
                {{ $logs->appends(request()->query())->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
