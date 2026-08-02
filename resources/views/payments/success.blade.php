<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Diterima - Serrata Kost</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="flex min-h-screen flex-col justify-center bg-gradient-to-b from-white via-brand-50/40 to-stone-100 px-4 py-10">
        <main class="mx-auto w-full max-w-md">
            <div class="card px-6 py-10 text-center animate-fade-in-up sm:px-10">
                <div class="mx-auto mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 text-emerald-500 ring-8 ring-emerald-50/60">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-extrabold tracking-tight text-stone-900 sm:text-3xl">
                    Pembayaran Diterima
                </h1>
                <p class="mt-2 text-sm leading-relaxed text-stone-500">
                    Terima kasih! Bukti pembayaran kamu sudah kami terima dan sedang diverifikasi oleh admin.
                </p>

                <div class="mt-8 divide-y divide-stone-100 rounded-2xl border border-stone-100 bg-stone-50/60 px-6 py-2 text-left">
                    <div class="flex items-center justify-between gap-4 py-3.5">
                        <span class="text-sm text-stone-500">No. Invoice</span>
                        <span class="font-mono text-sm font-bold text-stone-800">{{ $payment->invoice_number }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3.5">
                        <span class="text-sm text-stone-500">Total Dibayar</span>
                        <span class="tabular text-base font-extrabold text-brand-600">Rp {{ number_format($payment->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <a href="/" class="btn btn-primary mt-8 w-full">
                    Kembali ke Beranda
                </a>

                <p class="mt-5 text-[10px] font-semibold uppercase tracking-widest text-stone-400">
                    Serrata Kost &bull; 2026
                </p>
            </div>
        </main>
    </div>
</body>
</html>
