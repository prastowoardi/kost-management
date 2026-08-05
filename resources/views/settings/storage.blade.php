<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="page-title">Pengaturan Penyimpanan</h2>
                <p class="mt-1 text-sm text-stone-500">Pilih tempat penyimpanan file (bukti bayar, foto keluhan, kamar, dan penghuni).</p>
            </div>
        </div>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="page-container">
            <div class="mx-auto max-w-3xl space-y-6">

                @if($inconsistent['has'])
                    <div class="alert-error flex items-start gap-3 !border-red-200 !bg-red-50">
                        <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-7.5 12.5A2 2 0 003.6 19h16.8a2 2 0 001.71-3l-7.5-12.5a2 2 0 00-3.4 0z"/></svg>
                        <div>
                            <p class="font-bold text-red-700">Penyimpanan belum sinkron</p>
                            <p class="text-sm text-red-600">
                                Ada <b>{{ number_format($inconsistent['count']) }}</b> file yang masih di <b>{{ $inconsistent['disk'] }}</b>, sedangkan penyimpanan aktif sekarang <b>{{ $inconsistent['current'] }}</b>. File itu belum terbaca di halaman. Gunakan tombol migrasi di bawah untuk menyelaraskan.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header bg-stone-50/60">
                        <h3 class="section-title">Status Penyimpanan Aktif</h3>
                    </div>
                    <div class="card-body space-y-3">
                        <div class="flex items-center justify-between rounded-xl border border-stone-200 bg-white p-4">
                            <div>
                                <p class="text-sm font-bold text-stone-800">Disk sekarang</p>
                                <p class="text-xs text-stone-500">Dipakai untuk semua upload file baru</p>
                            </div>
                            <span class="{{ $currentDisk === 's3' ? 'badge-info' : 'badge-warning' }}">
                                {{ $disks[$currentDisk] ?? $currentDisk }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-xl border border-stone-200 bg-white p-4">
                            <div>
                                <p class="text-sm font-bold text-stone-800">Konfigurasi Cloudflare R2</p>
                                <p class="text-xs text-stone-500">Kredensial bucket pada file .env</p>
                            </div>
                            <span class="{{ $r2Configured ? 'badge-success' : 'badge-danger' }}">
                                {{ $r2Configured ? 'Terkonfigurasi' : 'Belum' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-stone-200 bg-white p-4 text-center">
                                <p class="text-2xl font-extrabold text-stone-800">{{ number_format($counts['public']) }}</p>
                                <p class="text-xs font-medium text-stone-500 mt-1">File di Lokal</p>
                            </div>
                            <div class="rounded-xl border border-stone-200 bg-white p-4 text-center">
                                <p class="text-2xl font-extrabold text-stone-800">{{ number_format($counts['s3']) }}</p>
                                <p class="text-xs font-medium text-stone-500 mt-1">File di R2</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-stone-50/60">
                        <h3 class="section-title">Pindah Penyimpanan</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $isR2 = $currentDisk === 's3';
                            $fromLabel = $isR2 ? 'R2' : 'Lokal';
                            $toLabel = $isR2 ? 'Lokal' : 'R2';
                            $toDisk = $isR2 ? 'public' : 's3';
                        @endphp
                        <p class="mb-4 text-sm text-stone-600">
                            Saat ini file tersimpan di <b>{{ $disks[$currentDisk] ?? $currentDisk }}</b>. Klik tombol di bawah untuk berpindah ke <b>{{ $toLabel }}</b>:
                        </p>

                        <div class="flex flex-wrap gap-3">
                            @if($isR2)
                                <form method="POST" action="{{ route('settings.storage.switch') }}" onsubmit="return confirmForm(event, 'Pindah penyimpanan ke Lokal? URL file akan berubah.')">
                                    @csrf
                                    <input type="hidden" name="disk" value="{{ $toDisk }}">
                                    <button type="submit" class="btn-secondary">
                                        {{ $fromLabel }} → {{ $toLabel }}
                                    </button>
                                </form>
                            @else
                                @if($r2Configured)
                                    <form method="POST" action="{{ route('settings.storage.switch') }}" onsubmit="return confirmForm(event, 'Pindah penyimpanan ke Cloudflare R2? URL file akan berubah.')">
                                        @csrf
                                        <input type="hidden" name="disk" value="{{ $toDisk }}">
                                        <button type="submit" class="btn-primary">
                                            {{ $fromLabel }} → {{ $toLabel }}
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center text-sm text-red-600">R2 belum dikonfigurasi di .env</span>
                                @endif
                            @endif
                        </div>

                        <p class="form-hint mt-4">Setelah switch, lanjutkan ke bagian migrasi file di bawah agar file lama ikut pindah.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-stone-50/60">
                        <h3 class="section-title">Migrasi File</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $migrateToS3 = $currentDisk === 's3';
                            $fromLabel = $migrateToS3 ? 'Lokal' : 'R2';
                            $toLabel = $migrateToS3 ? 'R2' : 'Lokal';
                            $sourceCount = $migrateToS3 ? $counts['public'] : $counts['s3'];
                            $sourceEmpty = $sourceCount === 0;
                        @endphp
                        <p class="mb-4 text-sm text-stone-600">
                            Pindahkan file yang sudah ada dari <b>{{ $fromLabel }}</b> ke <b>{{ $toLabel }}</b> agar semua berkas tetap dapat diakses.
                        </p>

                        <form method="POST" action="{{ route('settings.storage.migrate') }}" onsubmit="return confirmForm(event, 'Mulai migrasi {{ $sourceCount }} file dari {{ $fromLabel }} ke {{ $toLabel }}?')">
                            @csrf
                            <label class="mb-4 flex items-center gap-2 text-sm text-stone-700">
                                <input type="checkbox" name="delete_source" value="1" class="rounded border-stone-300 text-brand-600 focus:ring-brand-500">
                                Hapus file dari sumber setelah berhasil disalin
                            </label>
                            <div class="flex items-center gap-3">
                                <button type="submit" class="btn-primary" {{ $sourceEmpty ? 'disabled' : '' }}>
                                    Migrasi Sekarang ({{ $fromLabel }} → {{ $toLabel }})
                                </button>
                                <span class="text-xs text-stone-500">{{ number_format($sourceCount) }} file akan dipindah</span>
                            </div>
                            @if($sourceEmpty)
                                <p class="form-hint mt-3">Tidak ada file di {{ $fromLabel }} yang perlu dipindah.</p>
                            @endif
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>