<x-guest-layout>
    <div class="flex flex-col items-center text-center">
        <div class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 text-emerald-500 ring-8 ring-emerald-50/60">
            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h2 class="text-2xl font-extrabold tracking-tight text-stone-900 sm:text-3xl">
            Mantap, Udah Terdaftar!
        </h2>
        <p class="mt-3 text-sm leading-relaxed text-stone-500 sm:text-base">
            Data kamu sudah masuk ke sistem kami. Tim Serrata Kost bakal segera cek data kamu, tungguin ya!
        </p>

        @if(session('wa_error'))
            <div class="alert-warning mt-7 text-left">
                <svg class="h-5 w-5 shrink-0 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide">Eh, ada kendala dikit..</p>
                    <p class="mt-0.5 text-xs font-medium leading-normal">
                        {{ session('wa_error') }}
                    </p>
                </div>
            </div>
        @else
            <div class="alert-info mt-7 text-left">
                <svg class="h-5 w-5 shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <p class="text-xs font-medium leading-relaxed">
                    Coba intip WhatsApp kamu ya. Detail tata tertib udah mimin kirim ke sana.
                </p>
            </div>
        @endif

        <div class="mt-8 w-full space-y-3">
            <a href="/" class="btn btn-primary w-full">
                Oke, Sip!
            </a>
            <p class="pt-1 text-center text-[10px] font-semibold uppercase tracking-widest text-stone-400">
                Serrata Kost &bull; 2026
            </p>
        </div>
    </div>
</x-guest-layout>
