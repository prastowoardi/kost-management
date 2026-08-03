<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="page-title">
                {{ __('Penghuni') }}
            </h2>

            <a href="{{ route('tenants.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Penghuni
            </a>
        </div>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="page-container">

            {{-- Success Alert --}}
            @if(session('success'))
            <div class="alert-success mb-6">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
            @endif

            <form method="GET" action="{{ route('tenants.index') }}" class="mb-6">
                <x-filter-panel reset="{{ route('tenants.index') }}">
                    <x-filter-input name="search" label="Cari Penghuni" placeholder="Nama atau telepon..." />
                    <x-filter-select name="status" label="Status" :options="['active' => 'Aktif', 'inactive' => 'Tidak Aktif']" />
                    <x-filter-select name="room_id" label="Kamar" :options="$rooms->pluck('room_number', 'id')->toArray()" placeholder="Semua Kamar" />
                </x-filter-panel>
            </form>

            <div class="card">
                <div class="card-body">

                    <!-- TABLE WRAPPER -->
                    <div class="overflow-x-auto">
                        <table class="min-w-max w-full">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Kamar</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($tenants as $tenant)
                                <tr>
                                    <!-- NAMA -->
                                    <td>
                                        <div class="flex items-center gap-3">
                                            @if($tenant->photo)
                                                <img src="{{ asset('storage/' . $tenant->photo) }}"
                                                    class="h-10 w-10 rounded-full object-cover">
                                            @else
                                                <div class="avatar">
                                                    {{ substr($tenant->name, 0, 1) }}
                                                </div>
                                            @endif

                                            <span class="text-sm font-medium text-stone-900">
                                                {{ $tenant->name }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- EMAIL -->
                                    <td class="text-stone-500">
                                        {{ $tenant->email }}
                                    </td>

                                    <!-- TELEPON -->
                                    <td class="text-stone-500 tabular">
                                        {{ $tenant->phone }}
                                    </td>

                                    <!-- KAMAR -->
                                    <td class="font-medium text-stone-800">
                                        {{ $tenant->room->room_number ?? '-' }}
                                    </td>

                                    <!-- TANGGAL MASUK -->
                                    <td class="text-stone-500 tabular">
                                        {{ $tenant->entry_date->format('d M Y') }}
                                    </td>

                                    <!-- STATUS -->
                                    <td>
                                        <span class="{{ $tenant->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ ucfirst($tenant->status) }}
                                        </span>
                                    </td>

                                    <!-- AKSI -->
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('tenants.show', $tenant) }}" class="text-stone-400 transition hover:text-brand-600" title="Detail Penghuni">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>

                                            {{-- <a href="{{ route('broadcast.chat', $tenant->id) }}" class="text-green-600 hover:text-green-900 ml-2" title="Lihat Chat">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                            </a> --}}

                                            <a href="{{ route('tenants.edit', $tenant) }}" class="text-stone-400 transition hover:text-brand-600" title="Edit Penghuni">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>

                                            <form id="delete-tenant-{{ $tenant->id }}" action="{{ route('tenants.destroy', $tenant) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button data-confirm-delete="delete-tenant-{{ $tenant->id }}" data-item-name="Penghuni {{ $tenant->name }}" class="text-stone-400 transition hover:text-red-600" title="Hapus Penghuni">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state w-full">
                                            <svg class="mb-3 h-10 w-10 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            <p class="font-medium text-stone-500">Belum ada data penghuni</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    <!-- PAGINATION -->
                    <div class="mt-5">
                        {{ $tenants->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
