<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="page-title">
                {{ __('Daftar Keluhan') }}
            </h2>
            <a href="{{ route('complaints.create') }}" class="btn-primary btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Keluhan
            </a>
        </div>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="page-container space-y-6">

            {{-- Bagian Notifikasi Sukses --}}
            @if(session('success'))
            <div class="alert-success" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <form method="GET" action="{{ route('complaints.index') }}">
                <x-filter-panel reset="{{ route('complaints.index') }}">
                    <x-filter-input name="search" label="Cari Keluhan" placeholder="Judul keluhan..." />
                    <x-filter-select name="category" label="Kategori" :options="['facility' => 'Fasilitas', 'cleanliness' => 'Kebersihan', 'security' => 'Keamanan', 'other' => 'Lainnya']" />
                    <x-filter-select name="priority" label="Prioritas" :options="['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi']" />
                    <x-filter-select name="status" label="Status" :options="['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed']" />
                </x-filter-panel>
            </form>

            <div class="card">
                <div class="card-header">
                    <h3 class="section-title">Data Keluhan</h3>
                </div>
                <div class="card-body">

                    <div class="overflow-x-auto rounded-2xl border border-stone-100">
                        <table class="min-w-max w-full">
                            <thead>
                                <tr>
                                    <th class="w-1/5">Judul</th>
                                    <th class="hidden sm:table-cell">Penghuni</th>
                                    <th class="hidden md:table-cell">Kamar</th>
                                    <th class="hidden lg:table-cell">Kategori</th>
                                    <th>Prioritas</th>
                                    <th>Status</th>
                                    <th class="hidden xl:table-cell">Tanggal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($complaints as $complaint)
                                <tr>
                                    <td class="max-w-xs truncate font-medium text-stone-900">
                                        {{ $complaint->title }}
                                    </td>
                                    <td class="hidden whitespace-nowrap text-stone-500 sm:table-cell">
                                        {{ $complaint->tenant->name }}
                                    </td>
                                    <td class="hidden whitespace-nowrap text-stone-500 md:table-cell">
                                        {{ $complaint->room->room_number }}
                                    </td>
                                    <td class="hidden whitespace-nowrap lg:table-cell">
                                        <span class="badge-neutral">
                                            {{ ucfirst($complaint->category) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap">
                                        <span class="@if($complaint->priority == 'high') badge-danger @elseif($complaint->priority == 'medium') badge-warning @else badge-success @endif">
                                            {{ ucfirst($complaint->priority) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap">
                                        <span class="@if($complaint->status == 'open') badge-danger @elseif($complaint->status == 'in_progress') badge-warning @elseif($complaint->status == 'resolved') badge-success @else badge-neutral @endif">
                                            {{ str_replace('_', ' ', ucfirst($complaint->status)) }}
                                        </span>
                                    </td>
                                    <td class="hidden whitespace-nowrap text-stone-500 xl:table-cell">
                                        {{ $complaint->created_at->format('d M Y') }}
                                    </td>
                                    <td class="whitespace-nowrap text-center">
                                        <div class="flex flex-col space-y-1 sm:flex-row sm:space-x-1 sm:space-y-0 justify-center">
                                            <a href="{{ route('complaints.show', $complaint) }}" class="text-stone-400 hover:text-brand-600 transition" title="Detail Keluhan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('complaints.edit', $complaint) }}" class="text-stone-400 hover:text-brand-600 transition" title="Edit Keluhan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form id="delete-complaint-{{ $complaint->id }}" action="{{ route('complaints.destroy', $complaint) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button data-confirm-delete="delete-complaint-{{ $complaint->id }}" data-item-name="Keluhan ini" class="text-stone-400 hover:text-red-600 transition" title="Hapus Keluhan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="py-10 text-center text-stone-400">
                                        Belum ada data keluhan yang tercatat.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Bagian Pagination --}}
                    <x-pagination :paginator="$complaints" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
