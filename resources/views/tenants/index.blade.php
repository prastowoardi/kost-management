<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800">
                {{ __('Penghuni') }}
            </h2>

            <a href="{{ route('tenants.create') }}"
                class="inline-flex justify-center items-center px-3 py-2 sm:px-4 sm:py-2 bg-blue-600 text-white text-xs sm:text-sm font-semibold tracking-widest rounded-md hover:bg-blue-700">
                + Tambah Penghuni
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Success Alert --}}
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kamar</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Masuk</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($tenants as $tenant)
                                <tr>
                                    <!-- NAMA -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($tenant->photo)
                                                <img src="{{ asset('storage/' . $tenant->photo) }}"
                                                     class="h-10 w-10 rounded-full object-cover mr-3">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                                    <span class="font-bold text-gray-600">
                                                        {{ substr($tenant->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif

                                            <span class="text-sm font-medium text-gray-900">
                                                {{ $tenant->name }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- EMAIL -->
                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $tenant->email }}
                                    </td>

                                    <!-- TELEPON -->
                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $tenant->phone }}
                                    </td>

                                    <!-- KAMAR -->
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                        {{ $tenant->room->room_number ?? '-' }}
                                    </td>

                                    <!-- TANGGAL MASUK -->
                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $tenant->entry_date->format('d M Y') }}
                                    </td>

                                    <!-- STATUS -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full
                                            {{ $tenant->status == 'active'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($tenant->status) }}
                                        </span>
                                    </td>

                                    <!-- AKSI -->
                                    <td class="px-4 py-3 whitespace-nowrap text-sm space-y-1 sm:space-y-0 sm:space-x-3 sm:flex sm:items-center">
                                        <a href="{{ route('tenants.show', $tenant) }}"
                                            class="text-blue-600 hover:text-blue-900 block sm:inline">
                                            Detail
                                        </a>

                                        <a href="{{ route('tenants.edit', $tenant) }}"
                                            class="text-indigo-600 hover:text-indigo-900 block sm:inline">
                                            Edit
                                        </a>

                                        <form action="{{ route('tenants.destroy', $tenant) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus penghuni ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 block sm:inline">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                                        Belum ada data penghuni
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    <!-- PAGINATION -->
                    <div class="mt-4">
                        {{ $tenants->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
