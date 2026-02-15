<x-guest-layout>
        <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-xl text-center border border-gray-100">
            
            {{-- Icon Success dengan warna lebih soft --}}
            <div class="mb-6 inline-flex items-center justify-center w-20 h-20 bg-green-50 text-green-500 rounded-full">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h2 class="text-3xl font-black text-gray-900 mb-3 tracking-tight">Mantap, Udah Terdaftar! ✨</h2>
            <p class="text-gray-600 mb-8 leading-relaxed">
                Data kamu sudah masuk ke sistem kami. Tim Serrata Kost bakal segera cek data kamu, tungguin ya!
            </p>

            @if(session('wa_error'))
                <div class="mb-8 p-4 bg-orange-50 border-l-4 border-orange-500 text-left rounded-r-xl shadow-sm">
                    <div class="flex items-center mb-1">
                        <svg class="h-5 w-5 text-orange-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-orange-800 font-bold text-sm uppercase tracking-wide">Eh, ada kendala dikit..</span>
                    </div>
                    <p class="text-orange-700 text-xs leading-normal font-medium">
                        {{ session('wa_error') }}
                    </p>
                </div>
            @else
                {{-- Info Normal - Lebih Chill --}}
                <div class="mb-8 p-4 bg-blue-50 rounded-2xl flex items-start text-left border border-blue-100">
                    <div class="mr-3 mt-0.5 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-blue-700 text-xs font-medium leading-relaxed">
                        Coba intip WhatsApp kamu ya. Detail tata tertib udah mimin kirim ke sana. 👋
                    </p>
                </div>
            @endif

            <hr class="mb-8 border-gray-100">

            <div class="space-y-3">
                <a href="/" class="w-full flex justify-center items-center bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition duration-300 shadow-md">
                    Oke, Sip!
                </a>
                <p class="text-gray-400 text-[10px] uppercase font-semibold tracking-widest">Serrata Kost &bull; 2026</p>
            </div>
        </div>
</x-guest-layout>