<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Keluhan') }}
            </h2>
            <a href="{{ route('complaints.create') }}" 
                class="inline-flex justify-center items-center px-3 py-2 sm:px-4 sm:py-2 bg-blue-600 text-white text-xs sm:text-sm font-semibold tracking-widest rounded-md hover:bg-blue-700">
                + Tambah Keluhan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Bagian Notifikasi Sukses --}}
            @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Data Keluhan</h3>
                    
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            {{-- ... (Isi Tabel sama seperti sebelumnya) ... --}}
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/5">Judul</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell">Penghuni</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Kamar</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Prioritas</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden xl:table-cell">Tanggal</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($complaints as $complaint)
                                <tr>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 truncate max-w-xs">
                                        {{ $complaint->title }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                        {{ $complaint->tenant->name }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                        {{ $complaint->room->room_number }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap hidden lg:table-cell">
                                        <span class="px-2 inline-flex text-xs leading-5 font-medium rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($complaint->category) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-medium rounded-full 
                                            @if($complaint->priority == 'high') bg-red-100 text-red-800
                                            @elseif($complaint->priority == 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst($complaint->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-medium rounded-full 
                                            @if($complaint->status == 'resolved' || $complaint->status == 'closed') bg-green-100 text-green-800
                                            @elseif($complaint->status == 'in_progress') bg-blue-100 text-blue-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ str_replace('_', ' ', ucfirst($complaint->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 hidden xl:table-cell">
                                        {{ $complaint->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex flex-col space-y-1 sm:flex-row sm:space-x-2 sm:space-y-0 justify-center">
                                            <a href="{{ route('complaints.show', $complaint) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                                            <a href="{{ route('complaints.edit', $complaint) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                            <form action="{{ route('complaints.destroy', $complaint) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus keluhan ini? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 p-0 m-0 leading-none">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada data keluhan yang tercatat.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Bagian Pagination --}}
                    <div class="mt-6">
                        {{ $complaints->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>