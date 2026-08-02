<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Detail Keluhan') }}
        </h2>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="page-container">
            <div class="mx-auto max-w-4xl">
                <div class="card">
                    <div class="card-body p-6 sm:p-8">

                        {{-- HEADER & JUDUL --}}
                        <h3 class="border-b border-stone-100 pb-4 text-2xl font-extrabold tracking-tight text-stone-900 sm:text-3xl">
                            {{ $complaint->title }}
                        </h3>

                        <div class="mt-6 grid grid-cols-2 gap-4 text-sm lg:grid-cols-4">

                            {{-- Tanggal Dibuat --}}
                            <div class="border-r border-stone-100 pr-4">
                                <p class="font-medium text-stone-500">Dibuat</p>
                                <p class="text-base font-semibold text-stone-800">{{ $complaint->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-stone-400">{{ $complaint->created_at->format('H:i') }} WIB</p>
                            </div>

                            {{-- Status --}}
                            <div class="border-r border-stone-100 pr-4">
                                <p class="font-medium text-stone-500">Status</p>
                                @php
                                    $statusClass = [
                                        'open' => 'badge-danger',
                                        'in_progress' => 'badge-warning',
                                        'resolved' => 'badge-success',
                                        'closed' => 'badge-neutral',
                                    ][$complaint->status] ?? 'badge-neutral';
                                @endphp
                                <span class="{{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                </span>
                            </div>

                            {{-- Prioritas --}}
                            <div class="border-r border-stone-100 pr-4">
                                <p class="font-medium text-stone-500">Prioritas</p>
                                @php
                                    $priorityClass = [
                                        'high' => 'badge-danger',
                                        'medium' => 'badge-warning',
                                        'low' => 'badge-success',
                                    ][$complaint->priority] ?? 'badge-neutral';
                                @endphp
                                <span class="{{ $priorityClass }}">{{ ucfirst($complaint->priority) }}</span>
                            </div>

                            {{-- Kategori --}}
                            <div>
                                <p class="font-medium text-stone-500">Kategori</p>
                                <span class="badge-neutral">{{ ucfirst($complaint->category) }}</span>
                            </div>
                        </div>

                        {{-- INFORMASI PENGHUNI --}}
                        <div class="mt-6 rounded-2xl border border-stone-100 bg-stone-50/70 p-5">
                            <h4 class="section-title mb-3">Informasi Penghuni</h4>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Nama Penghuni</p>
                                    <p class="mt-1 font-semibold text-stone-900">{{ $complaint->tenant->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Nomor Kamar</p>
                                    <p class="mt-1 font-semibold text-stone-900">{{ $complaint->room->room_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- DESKRIPSI KELUHAN --}}
                        <div class="mt-6">
                            <h4 class="section-title mb-3">Deskripsi Detail</h4>
                            <div class="rounded-2xl border border-stone-100 bg-stone-50/70 p-5 whitespace-pre-line text-stone-700">{{ $complaint->description }}</div>
                        </div>

                        {{-- FOTO KELUHAN --}}
                        <div class="mt-6 border-t border-stone-100 pt-6">
                            <h4 class="section-title mb-3">
                                {{-- Hapus .count(), gunakan fungsi count() PHP bawaan --}}
                                Lampiran Foto Keluhan ({{ count($complaint->images ?? []) }})
                            </h4>

                            {{-- Tambahkan ?? [] untuk mengatasi kasus null jika tidak ada gambar --}}
                            @if(empty($complaint->images))
                                <p class="empty-state text-stone-400">Tidak ada foto yang dilampirkan pada keluhan ini.</p>
                            @else
                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                                    {{-- $imagePath adalah string, bukan object --}}
                                    @foreach($complaint->images as $imagePath)
                                        @php
                                            $imageUrl = asset('storage/' . $imagePath);
                                        @endphp
                                        <a href="{{ $imageUrl }}" target="_blank" class="group relative block overflow-hidden rounded-2xl border border-stone-100 shadow-sm transition duration-200 hover:shadow-lift">
                                            <img src="{{ $imageUrl }}"
                                                alt="Foto Keluhan"
                                                class="h-32 w-full object-cover transition-opacity duration-300 group-hover:opacity-80">
                                            {{-- Detail overlay tetap sama --}}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- RESPON/RESOLUSI --}}
                        @if($complaint->response)
                        <div class="mt-6 border-t border-stone-100 pt-6">
                            <h4 class="section-title mb-3 text-brand-700">Respon</h4>
                            <div class="rounded-r-2xl border-l-4 border-brand-500 bg-brand-50 p-5 shadow-sm">
                                <p class="whitespace-pre-line text-stone-800">{{ $complaint->response }}</p>
                                @if($complaint->resolved_date)
                                <p class="mt-2 text-xs text-stone-500">Diselesaikan pada: {{ $complaint->resolved_date->format('d M Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="mt-8 flex justify-end border-t border-stone-100 pt-6">
                            <a href="{{ route('complaints.index') }}" class="btn-ghost">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                            </a>
                            {{-- Tambahkan tombol Edit/Update jika pengguna adalah Admin/Manajer --}}
                            {{-- <a href="{{ route('complaints.edit', $complaint) }}" class="ml-3 px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition duration-150">
                                Edit Keluhan
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
