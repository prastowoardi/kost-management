<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4 text-gray-800">Broadcast WhatsApp</h2>

                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 shadow-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form action="{{ route('broadcast.send') }}" method="POST" class="mb-8">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pesan Broadcast</label>
                        <textarea name="message" rows="4" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                            placeholder="Tulis pesan Anda di sini..." required></textarea>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Kirim Sekarang
                    </button>
                </form>

                @if (session('deliveryLogs'))
                    @endif
            </div>
        </div>
    </div>
</x-app-layout>