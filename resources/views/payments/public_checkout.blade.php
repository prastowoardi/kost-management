<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayar Tagihan - Serrata Kost</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="flex min-h-screen flex-col bg-gradient-to-b from-white via-brand-50/40 to-stone-100 px-4 py-8 sm:py-14">
        <main class="mx-auto w-full max-w-lg">
            <header class="mb-8 text-center">
                <a href="/" class="inline-flex items-center gap-2.5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 shadow-soft">
                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    </span>
                    <span class="text-xl font-extrabold tracking-tight text-stone-900">
                        Serrata <span class="text-brand-600">Kost</span>
                    </span>
                </a>
                <p class="mt-2 text-sm text-stone-500">Pembayaran Tagihan Bulanan</p>
            </header>

            @if(session('success'))
                <div class="alert-success mb-6 animate-fade-in">
                    <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="card overflow-hidden">
                <div class="border-b border-stone-100 bg-stone-50/60 px-6 py-5 sm:px-7">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-stone-400">No. Invoice</p>
                            <p class="mt-1 font-mono text-sm font-bold text-stone-800">{{ $payment->invoice_number }}</p>
                        </div>

                        @if($payment->status === 'overdue')
                            <span class="badge badge-danger">Terlambat</span>
                        @else
                            <span class="badge badge-warning">Menunggu Konfirmasi</span>
                        @endif
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <dl class="space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-sm text-stone-500">Nama</dt>
                            <dd class="text-sm font-bold text-stone-800">{{ $payment->tenant->name }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-sm text-stone-500">Kamar</dt>
                            <dd class="text-sm font-bold text-stone-800">Kamar No. {{ $payment->tenant->room->room_number }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-sm text-stone-500">Periode</dt>
                            <dd class="text-sm font-bold text-stone-800">
                                {{ $payment->period_month ? \Carbon\Carbon::parse($payment->period_month)->translatedFormat('F Y') : 'Periode berjalan' }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-sm text-stone-500">Jatuh Tempo</dt>
                            <dd class="text-sm font-bold text-stone-800">
                                {{ \Carbon\Carbon::parse($payment->created_at)->addMonth()->translatedFormat('d F Y') }}
                            </dd>
                        </div>
                    </dl>

                    <div class="my-7 rounded-2xl bg-gradient-to-br from-brand-600 to-brand-700 px-6 py-7 text-center text-white shadow-soft">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-brand-100">Total Tagihan</p>
                        <p class="tabular mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">
                            Rp {{ number_format($payment->total, 0, ',', '.') }}
                        </p>
                        @if($payment->late_fee > 0)
                            <p class="mt-3 text-xs font-medium text-brand-100">
                                Termasuk denda Rp {{ number_format($payment->late_fee, 0, ',', '.') }}
                            </p>
                        @endif
                    </div>

                    <form action="{{ route('public.pay.upload', $hash) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <div>
                            <label for="proof" class="form-label">Upload Bukti Pembayaran</label>
                            <p class="form-hint">Lampirkan screenshot bukti transfer dalam format gambar (JPG / PNG), maksimal 2 MB.</p>
                            <input type="file" name="proof" id="proof" accept="image/*" required
                                class="mt-2 block w-full text-sm text-stone-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                            @error('proof')
                                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-full">
                            Kirim Bukti Pembayaran
                        </button>
                    </form>
                </div>
            </div>

            <footer class="mt-8 text-center">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-stone-400">
                    Serrata Kost &bull; 2026
                </p>
            </footer>
        </main>
    </div>
</body>
</html>
