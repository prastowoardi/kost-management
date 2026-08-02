<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Riwayat Broadcast WhatsApp') }}
        </h2>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="page-container">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="section-title">Daftar Pesan Terkirim</h3>
                        <p class="page-subtitle">Riwayat otomatis dihapus setelah 3 bulan.</p>
                    </div>
                    <a href="{{ route('broadcast.index') }}" class="btn-primary btn-sm">
                        &larr; Kirim Pesan Baru
                    </a>
                </div>
                <div class="card-body">

                    @if (session('status'))
                        <div class="alert-success mb-5">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="space-y-4">
                        @forelse ($history as $item)
                            <div class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm transition hover:shadow-lift">
                                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                    <div class="flex-1">
                                        <span class="badge-info">
                                            {{ $item->created_at->format('d M Y, H:i') }}
                                        </span>
                                        <p class="mt-3 leading-relaxed text-stone-700 whitespace-pre-line">{{ $item->message }}</p>
                                    </div>

                                    <div class="flex items-center gap-3 md:flex-col md:items-end md:gap-1.5">
                                        <span class="badge-success">
                                            <span class="text-sm font-extrabold">{{ $item->total_success }}</span>&nbsp;Berhasil
                                        </span>
                                        <span class="badge-danger">
                                            <span class="text-sm font-extrabold">{{ $item->total_failed }}</span>&nbsp;Gagal
                                        </span>
                                    </div>
                                </div>

                                <details class="group mt-4 border-t border-stone-100 pt-4">
                                    <summary class="cursor-pointer text-xs font-bold uppercase tracking-wider text-stone-500 transition group-hover:text-brand-600">
                                        Lihat Detail Status Penerima
                                    </summary>
                                    <div class="mt-3 overflow-x-auto">
                                        <table class="min-w-max w-full">
                                            <thead>
                                                <tr>
                                                    <th>Nama Tenant</th>
                                                    <th>Nomor HP</th>
                                                    <th>Status</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($item->logs as $log)
                                                    <tr>
                                                        <td class="text-stone-900">{{ $log->tenant_name }}</td>
                                                        <td class="text-stone-500">{{ $log->phone }}</td>
                                                        <td>
                                                            <span class="{{ $log->status === 'success' ? 'badge-success' : 'badge-danger' }}">
                                                                {{ strtoupper($log->status) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-xs text-stone-400">
                                                            {{ $log->error_message ?? '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </details>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-stone-100 text-stone-400">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </div>
                                <p class="text-sm font-medium text-stone-500">Belum ada riwayat broadcast yang dikirim.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $history->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
