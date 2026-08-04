<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="page-title">
                {{ __('Kamar') }}
            </h2>
            <a href="{{ route('rooms.create') }}" class="btn-primary btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kamar
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

            @if(session('error'))
            <div class="alert-error mb-4">
                {{ session('error') }}
            </div>
            @endif

            <form method="GET" action="{{ route('rooms.index') }}" class="mb-6">
                <x-filter-panel reset="{{ route('rooms.index') }}">
                    <x-filter-input name="search" label="Cari Kamar" placeholder="No kamar..." />
                    <x-filter-select name="type" label="Tipe" :options="['singlenoac' => 'Single No AC', 'singleac' => 'Single AC', 'shared' => 'Shared Room']" />
                    <x-filter-select name="status" label="Status" :options="['available' => 'Available', 'occupied' => 'Occupied', 'maintenance' => 'Maintenance']" />
                    <x-filter-input name="price_min" label="Harga Min" placeholder="Rp 0" />
                    <x-filter-input name="price_max" label="Harga Max" placeholder="Rp 999.999" />
                </x-filter-panel>
            </form>

            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="min-w-max w-full">
                            <thead>
                                <tr>
                                    <th>No Kamar</th>
                                    <th>Tipe</th>
                                    <th>Harga</th>
                                    <th>Kapasitas</th>
                                    <th>Status</th>
                                    <th>Penghuni</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rooms as $room)
                                <tr>
                                    <td class="font-semibold text-stone-900">
                                        {{ $room->room_number }}
                                    </td>
                                    <td>
                                        @php
                                            $typeStyles = [
                                                'singlenoac' => 'badge-success',
                                                'singleac'   => 'badge bg-sky-50 text-sky-700 ring-1 ring-sky-200/70',
                                                'shared'     => 'badge bg-violet-50 text-violet-700 ring-1 ring-violet-200/70',
                                            ];
                                            $typeNames = [
                                                'singlenoac' => 'Single No AC',
                                                'singleac'   => 'Single AC',
                                                'shared'     => 'Shared Room',
                                            ];
                                            $style = $typeStyles[$room->type] ?? 'badge-neutral';
                                            $name = $typeNames[$room->type] ?? ucfirst($room->type);
                                        @endphp
                                        <span class="{{ $style }}">
                                            {{ $name }}
                                        </span>
                                    </td>
                                    <td class="tabular text-stone-700">
                                        Rp {{ number_format($room->price, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        {{ $room->capacity }} orang
                                    </td>
                                    <td>
                                        <span class="@if($room->status == 'available') badge-success
                                            @elseif($room->status == 'occupied') badge bg-sky-50 text-sky-700 ring-1 ring-sky-200/70
                                            @else badge-warning @endif">
                                            {{ ucfirst($room->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($room->activeTenant)
                                            {{ $room->activeTenant->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('rooms.show', $room) }}" class="p-1.5 text-stone-400 transition hover:text-brand-600" title="Detail Kamar">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('rooms.edit', $room) }}" class="p-1.5 text-stone-400 transition hover:text-brand-600" title="Edit Kamar">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form id="delete-room-{{ $room->id }}" action="{{ route('rooms.destroy', $room) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button data-confirm-delete="delete-room-{{ $room->id }}" data-item-name="Kamar {{ $room->room_number }}" class="p-1.5 text-stone-400 transition hover:text-red-600" title="Hapus Kamar">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="!whitespace-normal">
                                        <div class="empty-state">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-stone-100">
                                                <svg class="h-6 w-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"/></svg>
                                            </div>
                                            <p class="mt-3 text-sm font-medium text-stone-600">Belum ada data kamar</p>
                                            <p class="mt-1 text-xs text-stone-400">Mulai dengan menambahkan kamar pertama Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $rooms->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
