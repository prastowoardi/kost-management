<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Broadcast WhatsApp</h2>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="page-container">
            <div class="mx-auto max-w-2xl">
                <div class="card">
                    <div class="card-body">

                        @if (session('status'))
                            <div class="alert-success mb-6 flex items-center">
                                <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        <form action="{{ route('broadcast.send') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Pesan Broadcast</label>
                                <textarea name="message" rows="5"
                                    class="mt-1 block w-full"
                                    placeholder="Tulis pesan Anda di sini..." required></textarea>
                            </div>
                            <div class="mt-6 flex items-center justify-end">
                                <button type="submit" class="btn-primary">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    Kirim Sekarang
                                </button>
                            </div>
                        </form>

                        @if (session('deliveryLogs'))
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
