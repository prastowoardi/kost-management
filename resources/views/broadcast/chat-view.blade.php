<x-app-layout>
    <div class="py-6">
        <div class="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="card flex h-[85vh] flex-col overflow-hidden sm:h-[80vh]">

                {{-- Header --}}
                <div class="flex items-center justify-between bg-brand-600 p-3 text-white shadow-md sm:p-4">
                    <div class="flex min-w-0 items-center">
                        <a href="{{ route('tenants.index') }}" class="mr-2 rounded-full p-1.5 transition hover:bg-white/10 sm:mr-3">
                            <svg class="h-5 w-5 text-white sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="truncate text-base font-bold leading-none text-white sm:text-lg">{{ $tenant->name }}</p>

                                @if($tenant->room)
                                    <span class="shrink-0 rounded bg-white/20 px-1.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider text-white">
                                        KAMAR {{ $tenant->room->room_number }}
                                    </span>
                                @else
                                    <span class="shrink-0 rounded bg-red-400 px-1.5 py-0.5 text-[10px] font-bold text-white">
                                        Belum Pilih Kamar
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="shrink-0 rounded-lg bg-white/20 px-2 py-1 text-xs font-medium text-white">
                        {{ $tenant->phone }}
                    </div>
                </div>

                {{-- Messages --}}
                <div
                    x-data="{ scrollToBottom() { $el.scrollTop = $el.scrollHeight; } }"
                    x-init="scrollToBottom(); setTimeout(() => scrollToBottom(), 100)"
                    class="chat-scroll flex-1 space-y-3 overflow-y-auto bg-stone-50 p-3 sm:p-4"
                >
                    @forelse($chats as $chat)
                        <div class="flex {{ $chat['fromMe'] ? 'justify-end' : 'justify-start' }}">
                            <div
                                class="relative max-w-[85%] px-3 py-2 shadow-sm sm:px-4
                                {{ $chat['fromMe'] ? 'rounded-2xl rounded-br-md bg-brand-600 text-white' : 'rounded-2xl rounded-bl-md bg-stone-100 text-stone-800' }}"
                            >
                                <p class="text-[14.5px] leading-snug">{{ $chat['body'] }}</p>
                                <div class="mt-1 flex items-center justify-end gap-1">
                                    <span class="text-[9px] font-medium {{ $chat['fromMe'] ? 'text-white/70' : 'text-stone-400' }}">{{ $chat['timestamp'] }}</span>
                                    @if($chat['fromMe'])
                                        <svg class="h-3 w-3 text-white/70" fill="currentColor" viewBox="0 0 24 24"><path d="M22.31 6.5c-.2-.19-.51-.21-.71-.02L9.22 18.01l-6.82-6.82a.501.501 0 1 0-.71.71l7.18 7.18c.1.1.23.15.35.15.13 0 .25-.05.35-.15L22.29 7.21c.2-.19.22-.51.02-.71z"/></svg>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex h-full flex-col items-center justify-center text-center">
                            <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm">💬</div>
                            <p class="text-sm italic text-stone-500">{{ $error ?? 'Belum ada obrolan hari ini.' }}</p>
                        </div>
                    @endforelse
                </div>

                {{-- Input Bar --}}
                <div class="border-t border-stone-100 bg-white p-3">
                    <form action="{{ route('broadcast.send-personal') }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="phone" value="{{ $tenant->phone }}">
                        <input
                            type="text"
                            name="message"
                            placeholder="Ketik pesan..."
                            required
                            autofocus
                            class="flex-1 rounded-full border-stone-200 bg-stone-50 px-5 py-2.5 text-sm shadow-sm focus:ring-brand-500"
                        >
                        <button type="submit" class="shrink-0 rounded-full bg-brand-600 p-2.5 text-white shadow-soft transition-all hover:bg-brand-700 active:scale-90">
                            <svg class="h-5 w-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" /></svg>
                        </button>
                    </form>
                </div>
            </div>

            <p class="mt-4 text-center text-[10px] uppercase tracking-widest text-stone-400">End-to-end Encrypted Dashboard</p>
        </div>
    </div>

    <style>
        /* Sembunyikan Scrollbar tapi tetap bisa scroll */
        .chat-scroll::-webkit-scrollbar { width: 4px; }
        .chat-scroll::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.1); border-radius: 10px; }
    </style>
</x-app-layout>
