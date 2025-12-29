<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex flex-col h-[80vh] border border-gray-200">
                
                <div class="bg-[#075e54] p-4 flex items-center justify-between text-white shadow-md">
                        <div class="flex items-center">
                            <a href="{{ route('tenants.index') }}" class="mr-3 hover:bg-black/10 p-1 rounded-full transition">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </a>
                            <div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <p class="font-bold leading-none text-white text-lg">{{ $tenant->name }}</p>
                                        
                                        @if($tenant->room)
                                            <span class="bg-yellow-400 text-gray-900 text-[10px] font-extrabold px-1.5 py-0.5 rounded shadow-sm uppercase tracking-wider">
                                                KAMAR {{ $tenant->room->room_number }}
                                            </span>
                                        @else
                                            <span class="bg-red-400 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm">
                                                Belum Pilih Kamar
                                            </span>
                                        @endif
                                    </div>
                                    {{-- <p class="text-[11px] text-green-200 mt-1 flex items-center">
                                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5 animate-pulse"></span>
                                        WhatsApp Personal
                                    </p> --}}
                                </div>
                            </div>
                        </div>
                    <div class="text-xs bg-black/20 px-2 py-1 rounded">
                        {{ $tenant->phone }}
                    </div>
                </div>

                <div 
                    x-data="{ scrollToBottom() { $el.scrollTop = $el.scrollHeight; } }" 
                    x-init="scrollToBottom(); setTimeout(() => scrollToBottom(), 100)"
                    class="flex-1 overflow-y-auto p-4 space-y-3 bg-[#efe7dd]"
                    style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-blend-mode: overlay;"
                >
                    @forelse($chats as $chat)
                        <div class="flex {{ $chat['fromMe'] ? 'justify-end' : 'justify-start' }}">
                            <div 
                                class="relative max-w-[85%] px-3 py-1.5 shadow-sm 
                                {{ $chat['fromMe'] ? 'bg-[#dcf8c6] rounded-l-lg rounded-br-lg' : 'bg-white rounded-r-lg rounded-bl-lg' }}"
                            >
                                <p class="text-[14.5px] text-gray-800 leading-snug">{{ $chat['body'] }}</p>
                                <div class="flex items-center justify-end mt-1 space-x-1">
                                    <span class="text-[9px] text-gray-400 font-medium">{{ $chat['timestamp'] }}</span>
                                    @if($chat['fromMe'])
                                        <svg class="w-3 h-3 text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M22.31 6.5c-.2-.19-.51-.21-.71-.02L9.22 18.01l-6.82-6.82a.501.501 0 1 0-.71.71l7.18 7.18c.1.1.23.15.35.15.13 0 .25-.05.35-.15L22.29 7.21c.2-.19.22-.51.02-.71z"/></svg>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-gray-500 opacity-60">
                            <div class="bg-white p-4 rounded-full shadow-sm mb-2">💬</div>
                            <p class="text-sm italic">{{ $error ?? 'Belum ada obrolan hari ini.' }}</p>
                        </div>
                    @endforelse
                </div>

                <div class="bg-[#f0f0f0] p-3 border-t border-gray-200">
                    <form action="{{ route('broadcast.send-personal') }}" method="POST" class="flex items-center space-x-2">
                        @csrf
                        <input type="hidden" name="phone" value="{{ $tenant->phone }}">
                        <input 
                            type="text" 
                            name="message" 
                            placeholder="Ketik pesan..." 
                            required 
                            autofocus
                            class="flex-1 border-none rounded-full px-5 py-2.5 text-sm focus:ring-2 focus:ring-[#075e54] shadow-sm"
                        >
                        <button type="submit" class="bg-[#075e54] hover:bg-[#128c7e] text-white p-2.5 rounded-full shadow-md transition-all active:scale-90">
                            <svg class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" /></svg>
                        </button>
                    </form>
                </div>
            </div>
            
            <p class="text-center text-[10px] text-gray-400 mt-4 uppercase tracking-widest">End-to-end Encrypted Dashboard</p>
        </div>
    </div>

    <style>
        /* Sembunyikan Scrollbar tapi tetap bisa scroll */
        .flex-1::-webkit-scrollbar { width: 4px; }
        .flex-1::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.1); border-radius: 10px; }
    </style>
</x-app-layout>