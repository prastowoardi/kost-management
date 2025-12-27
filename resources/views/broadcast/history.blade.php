<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Broadcast WhatsApp') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Daftar Pesan Terkirim</h3>
                        <p class="text-sm text-gray-500">Riwayat otomatis dihapus setelah 3 bulan.</p>
                    </div>
                    <a href="{{ route('broadcast.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                        &larr; Kirim Pesan Baru
                    </a>
                </div>

                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="space-y-4">
                    @forelse ($history as $item)
                        <div class="border border-gray-200 rounded-lg p-5 hover:bg-gray-50 transition relative">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-start pr-8">
                                <div class="flex-1">
                                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">
                                        {{ $item->created_at->format('d M Y, H:i') }}
                                    </span>
                                    <p class="mt-3 text-gray-700 leading-relaxed whitespace-pre-line">{{ $item->message }}</p>
                                </div>
                                
                                <div class="mt-4 md:mt-0 md:text-right flex space-x-4 md:block md:space-x-0">
                                    <div class="text-sm font-medium text-green-600">
                                        <span class="text-lg font-bold">{{ $item->total_success }}</span> Berhasil
                                    </div>
                                    <div class="text-sm font-medium text-red-600">
                                        <span class="text-lg font-bold">{{ $item->total_failed }}</span> Gagal
                                    </div>
                                </div>
                            </div>

                            <details class="mt-4 border-t border-gray-100 pt-4 group">
                                <summary class="text-xs font-bold text-gray-500 cursor-pointer uppercase tracking-wider group-hover:text-indigo-600 transition">
                                    Lihat Detail Status Penerima
                                </summary>
                                <div class="mt-3 overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 border rounded-md">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nama Tenant</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nomor HP</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-100">
                                            @foreach ($item->logs as $log)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $log->tenant_name }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $log->phone }}</td>
                                                    <td class="px-4 py-2 text-sm font-semibold {{ $log->status === 'success' ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ strtoupper($log->status) }}
                                                    </td>
                                                    <td class="px-4 py-2 text-xs text-gray-400">
                                                        {{ $log->error_message ?? '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        </div>
                    @empty
                        <div class="text-center py-12 border-2 border-dashed border-gray-200 rounded-lg">
                            <p class="text-gray-500">Belum ada riwayat broadcast yang dikirim.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $history->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>