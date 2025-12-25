<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800">
                {{ __('Fasilitas') }}
            </h2>

            <a href="{{ route('facilities.create') }}"
                class="inline-flex justify-center items-center px-3 py-2 sm:px-4 sm:py-2 bg-blue-600 text-white text-xs sm:text-sm font-semibold tracking-widest rounded-md hover:bg-blue-700">
                + Tambah Fasilitas
            </a>
        </div>
    </x-slot>


    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-4 sm:p-6">

                    <!-- TABLE WRAPPER -->
                    <div class="overflow-x-auto">
                        <table class="min-w-max w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Fasilitas</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($facilities as $facility)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">
                                        {{ $facility->name }}
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $facility->type == 'room' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $facility->type == 'room' ? 'Fasilitas Kamar' : 'Fasilitas Umum' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $facility->quantity }}
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($facility->condition == 'good') bg-green-100 text-green-800
                                            @elseif($facility->condition == 'fair') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($facility->condition) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        {{ Str::limit($facility->description, 50) }}
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap text-sm space-y-1 sm:space-y-0 sm:space-x-2 sm:flex sm:items-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('facilities.edit', $facility) }}" class="text-indigo-600 hover:text-indigo-900 block sm:inline">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form id="delete-facility-{{ $facility->id }}" action="{{ route('facilities.destroy', $facility) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button onclick="confirmDelete(event, 'delete-facility-{{ $facility->id }}', 'Fasilitas {{ $facility->name }}')" class="text-red-600 hover:text-red-900" title="Hapus Fasilitas">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                        Belum ada data fasilitas
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    <!-- PAGINATION -->
                    <div class="mt-4">
                        {{ $facilities->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
