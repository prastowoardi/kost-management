<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="page-title">
                {{ __('Detail Kamar') }} {{ $room->room_number }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('rooms.edit', $room) }}" class="btn-primary btn-sm">
                    ✏️ Edit
                </a>
                <a href="{{ route('rooms.index') }}" class="btn-secondary btn-sm">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="page-container">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Images Gallery -->
                    @php
                        $images = is_string($room->images) ? json_decode($room->images, true) : $room->images;
                        $firstImage = $images && count($images) > 0 ? Storage::url($images[0]) : null;
                    @endphp

                    @if($images && count($images) > 0)
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <h3 class="section-title">
                                📸 Foto Kamar ({{ count($images) }} foto)
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Main Image -->
                            <div class="mb-4">
                                <img id="mainImage"
                                        src="{{ $firstImage }}"
                                        alt="Foto Kamar Utama"
                                        class="w-full h-72 md:h-96 object-cover rounded-xl border-2 border-stone-200 cursor-pointer hover:border-brand-500 transition"
                                        data-full-src="{{ $firstImage }}">
                            </div>

                            <!-- Thumbnail Grid -->
                            <div class="grid grid-cols-4 md:grid-cols-5 gap-3">
                                @foreach($images as $index => $image)
                                @php $imagePath = Storage::url($image); @endphp
                                <div class="relative group">
                                    <img src="{{ $imagePath }}"
                                            alt="Foto Kamar {{ $index + 1 }}"
                                            class="w-full h-20 object-cover rounded-xl border-2 border-stone-200 cursor-pointer hover:border-brand-500 transition {{ $index === 0 ? 'ring-2 ring-brand-500' : '' }}"
                                            data-full-src="{{ $imagePath }}">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                        </svg>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state">
                                <svg class="h-12 w-12 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-stone-500">Belum ada foto kamar</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Room Details -->
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <h3 class="section-title">Informasi Kamar</h3>
                        </div>
                        <div class="card-body">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Nomor Kamar</p>
                                    <p class="mt-1 font-semibold text-stone-900">{{ $room->room_number }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Tipe</p>
                                    <p class="mt-1 font-semibold text-stone-900">{{ ucfirst($room->type) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Harga per Bulan</p>
                                    <p class="mt-1 font-semibold tabular text-stone-900">Rp {{ number_format($room->price, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Kapasitas</p>
                                    <p class="mt-1 font-semibold text-stone-900">{{ $room->capacity }} orang</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Ukuran</p>
                                    <p class="mt-1 font-semibold text-stone-900">{{ $room->size ? $room->size . ' m²' : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Status</p>
                                    <span class="mt-1 inline-flex @if($room->status == 'available') badge-success
                                        @elseif($room->status == 'occupied') badge bg-sky-50 text-sky-700 ring-1 ring-sky-200/70
                                        @else badge-warning @endif">
                                        {{ ucfirst($room->status) }}
                                    </span>
                                </div>
                            </div>

                            @if($room->description)
                            <div class="mt-6 border-t border-stone-100 pt-5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-stone-400 mb-2">Deskripsi</p>
                                <p class="text-sm leading-relaxed text-stone-700">{{ $room->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Facilities -->
                    @if($room->facilities->count() > 0)
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <h3 class="section-title">Fasilitas</h3>
                        </div>
                        <div class="card-body">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($room->facilities as $facility)
                                <div class="flex items-center gap-2 rounded-xl border border-stone-100 bg-stone-50/60 px-3 py-2.5 text-sm">
                                    <svg class="h-5 w-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-stone-700">{{ $facility->name }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                <!-- Sidebar -->
                <div class="space-y-6">

                    <!-- Current Tenant -->
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <h3 class="section-title">Penghuni Saat Ini</h3>
                        </div>
                        <div class="card-body">
                            @if($room->activeTenant)
                            <div class="flex items-center gap-3 mb-4">
                                @if($room->activeTenant->photo)
                                <img src="{{ Storage::url($room->activeTenant->photo) }}" class="h-12 w-12 rounded-full object-cover ring-2 ring-brand-100" alt="{{ $room->activeTenant->name }}">
                                @else
                                <div class="avatar h-12 w-12 text-base">
                                    <span>{{ substr($room->activeTenant->name, 0, 1) }}</span>
                                </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-stone-900">{{ $room->activeTenant->name }}</p>
                                    <p class="truncate text-sm text-stone-500">{{ $room->activeTenant->email }}</p>
                                </div>
                            </div>
                            <a href="{{ route('tenants.show', $room->activeTenant) }}" class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-700">
                                Lihat Detail →
                            </a>
                            @else
                            <div class="text-center py-6">
                                <svg class="mx-auto h-10 w-10 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="mt-2 text-sm text-stone-500">Kamar kosong</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <h3 class="section-title">Riwayat Pembayaran</h3>
                        </div>
                        <div class="card-body">
                            @if($room->payments->count() > 0)
                            <div class="space-y-3">
                                @foreach($room->payments->take(5) as $payment)
                                <div class="border-b border-stone-100 pb-3">
                                    <div class="flex justify-between items-start gap-2">
                                        <div>
                                            <p class="text-sm font-medium text-stone-900">{{ $payment->period_month->format('M Y') }}</p>
                                            <p class="text-xs text-stone-500">{{ $payment->payment_date->format('d M Y') }}</p>
                                        </div>
                                        <span class="{{ $payment->status == 'paid' ? 'badge-success' : 'badge-warning' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm font-semibold tabular text-stone-900">Rp {{ number_format($payment->total, 0, ',', '.') }}</p>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-center py-4 text-sm text-stone-500">Belum ada pembayaran</p>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Complaints -->
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <h3 class="section-title">Keluhan Terbaru</h3>
                        </div>
                        <div class="card-body">
                            @if($room->complaints->count() > 0)
                            <div class="space-y-3">
                                @foreach($room->complaints->take(3) as $complaint)
                                <div class="border-b border-stone-100 pb-3">
                                    <p class="text-sm font-medium text-stone-900">{{ $complaint->title }}</p>
                                    <div class="flex justify-between items-center mt-1 gap-2">
                                        <p class="text-xs text-stone-500">{{ $complaint->created_at->format('d M Y') }}</p>
                                        <span class="{{ $complaint->status == 'resolved' ? 'badge-success' : 'badge-warning' }}">
                                            {{ ucfirst($complaint->status) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-center py-4 text-sm text-stone-500">Belum ada keluhan</p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal (Full Screen View) -->
    <div id="imageModal"
            class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
        <button id="modalCloseBtn"
                class="absolute top-4 right-4 text-white hover:text-gray-300 transition">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="max-w-6xl max-h-full w-full flex items-center justify-center">
            <img id="modalImage"
                    src=""
                    alt="Full Image"
                    class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/pages/rooms-show.js')
    @endpush
</x-app-layout>
