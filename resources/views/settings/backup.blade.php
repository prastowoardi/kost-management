<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="page-title">Backup Database</h2>
                <p class="mt-1 text-sm text-stone-500">Cadangkan database secara manual atau otomatis setiap hari pukul 02:00.</p>
            </div>
        </div>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="page-container">
            <div class="mx-auto max-w-3xl space-y-6">

                @if(session('success'))
                <div class="alert-success mb-4">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert-error mb-4">
                    {{ session('error') }}
                </div>
                @endif

                <div class="card">
                    <div class="card-header bg-stone-50/60">
                        <h3 class="section-title">Buat Backup Sekarang</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.backup.store') }}" class="flex flex-wrap items-end gap-4">
                            @csrf
                            <div class="form-group !mb-0">
                                <label for="keep" class="form-label">Simpan Backup Terakhir (file)</label>
                                <input type="number" id="keep" name="keep" value="30" min="1" max="365"
                                    class="mt-1 block w-40 rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <button type="submit" class="btn-primary">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Backup Sekarang
                            </button>
                        </form>
                        <p class="form-hint mt-3">File backup disimpan di <code class="rounded bg-stone-100 px-1.5 py-0.5 text-xs">storage/app/backups</code>. Backup otomatis dijalankan scheduler setiap hari pukul 02:00 dan menyimpan 30 backup terakhir.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-stone-50/60">
                        <h3 class="section-title">Riwayat Backup ({{ count($files) }})</h3>
                    </div>
                    <div class="card-body">
                        @if(count($files) === 0)
                            <div class="empty-state">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-stone-100">
                                    <svg class="h-6 w-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                </div>
                                <p class="mt-3 text-sm font-medium text-stone-600">Belum ada backup</p>
                                <p class="mt-1 text-xs text-stone-400">Buat backup pertama Anda dari form di atas.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-max w-full">
                                    <thead>
                                        <tr>
                                            <th>Nama File</th>
                                            <th>Ukuran</th>
                                            <th>Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($files as $file)
                                        <tr>
                                            <td class="font-mono text-xs font-semibold text-stone-800">{{ $file['name'] }}</td>
                                            <td class="tabular text-stone-700">{{ number_format($file['size'], 2, ',', '.') }} MB</td>
                                            <td class="text-stone-600">{{ $file['created_at']->format('d M Y, H:i') }}</td>
                                            <td>
                                                <div class="flex items-center gap-1">
                                                    <a href="{{ route('settings.backup.download', $file['name']) }}" class="p-1.5 text-stone-400 transition hover:text-brand-600" title="Download Backup">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    </a>
                                                    <form id="delete-backup-{{ $loop->index }}" action="{{ route('settings.backup.destroy', $file['name']) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                    <button data-confirm-delete="delete-backup-{{ $loop->index }}" data-item-name="Backup {{ $file['name'] }}" class="p-1.5 text-stone-400 transition hover:text-red-600" title="Hapus Backup">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>