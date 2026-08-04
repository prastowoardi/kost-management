<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="page-title">
                {{ __('Fasilitas') }}
            </h2>

            <a href="{{ route('facilities.create') }}" class="btn-primary btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Fasilitas
            </a>
        </div>
    </x-slot>


    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="page-container">

            @if(session('success'))
            <div class="alert-success mb-4">
                {{ session('success') }}
            </div>
            @endif

            <form method="GET" action="{{ route('facilities.index') }}" class="mb-6">
                <x-filter-panel reset="{{ route('facilities.index') }}">
                    <x-filter-input name="search" label="Cari Fasilitas" placeholder="Nama fasilitas..." />
                    <x-filter-select name="type" label="Tipe" :options="['room' => 'Fasilitas Kamar', 'common' => 'Fasilitas Umum']" />
                    <x-filter-select name="condition" label="Kondisi" :options="['good' => 'Baik', 'fair' => 'Sedang', 'poor' => 'Rusak']" />
                </x-filter-panel>
            </form>

            <div class="card overflow-hidden">
                <div class="card-body">

                    <!-- TABLE WRAPPER -->
                    <div class="overflow-x-auto">
                        <table class="min-w-max w-full">

                            <thead>
                                <tr>
                                    <th>Nama Fasilitas</th>
                                    <th>Tipe</th>
                                    <th>Jumlah</th>
                                    <th>Kondisi</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($facilities as $facility)
                                <tr>
                                    <td class="font-semibold text-stone-900 whitespace-normal">
                                        {{ $facility->name }}
                                    </td>

                                    <td>
                                        <span class="badge @if($facility->type == 'room') bg-sky-50 text-sky-700 ring-1 ring-sky-200/70 @else bg-violet-50 text-violet-700 ring-1 ring-violet-200/70 @endif">
                                            {{ $facility->type == 'room' ? 'Fasilitas Kamar' : 'Fasilitas Umum' }}
                                        </span>
                                    </td>

                                    <td class="tabular text-stone-700">
                                        {{ $facility->quantity }}
                                    </td>

                                    <td>
                                        <span class="@if($facility->condition == 'good') badge-success
                                            @elseif($facility->condition == 'fair') badge-warning
                                            @else badge-danger @endif">
                                            {{ ucfirst($facility->condition) }}
                                        </span>
                                    </td>

                                    <td class="whitespace-normal text-stone-500 max-w-xs">
                                        {{ Str::limit($facility->description, 50) }}
                                    </td>

                                    <td>
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('facilities.edit', $facility) }}" class="p-1.5 text-stone-400 transition hover:text-brand-600" title="Edit Fasilitas">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form id="delete-facility-{{ $facility->id }}" action="{{ route('facilities.destroy', $facility) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button data-confirm-delete="delete-facility-{{ $facility->id }}" data-item-name="Fasilitas {{ $facility->name }}" class="p-1.5 text-stone-400 transition hover:text-red-600" title="Hapus Fasilitas">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="!whitespace-normal">
                                        <div class="empty-state">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-stone-100">
                                                <svg class="h-6 w-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                            </div>
                                            <p class="mt-3 text-sm font-medium text-stone-600">Belum ada data fasilitas</p>
                                            <p class="mt-1 text-xs text-stone-400">Mulai dengan menambahkan fasilitas pertama Anda.</p>
                                        </div>
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
