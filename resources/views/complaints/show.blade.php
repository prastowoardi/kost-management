<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Keluhan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    
                    {{-- HEADER & JUDUL --}}
                    <h3 class="text-3xl font-bold text-gray-900 mb-4 border-b pb-2">
                        {{ $complaint->title }}
                    </h3>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 text-sm">
                        
                        {{-- Tanggal Dibuat --}}
                        <div class="border-r pr-4">
                            <p class="font-medium text-gray-500">Dibuat</p>
                            <p class="text-base font-semibold text-gray-700">{{ $complaint->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $complaint->created_at->format('H:i') }} WIB</p>
                        </div>
                        
                        {{-- Status --}}
                        <div class="border-r pr-4">
                            <p class="font-medium text-gray-500">Status</p>
                            @php
                                $statusClass = [
                                    'open' => 'bg-red-100 text-red-800',
                                    'in_progress' => 'bg-yellow-100 text-yellow-800',
                                    'resolved' => 'bg-green-100 text-green-800',
                                    'closed' => 'bg-blue-100 text-blue-800',
                                ][$complaint->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                            </span>
                        </div>

                        {{-- Prioritas --}}
                        <div class="border-r pr-4">
                            <p class="font-medium text-gray-500">Prioritas</p>
                            <p class="text-base text-gray-700">{{ ucfirst($complaint->priority) }}</p>
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <p class="font-medium text-gray-500">Kategori</p>
                            <p class="text-base text-gray-700">{{ ucfirst($complaint->category) }}</p>
                        </div>
                    </div>

                    {{-- INFORMASI PENGHUNI --}}
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-inner">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Informasi Penghuni</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nama Penghuni</p>
                                <p class="text-base text-gray-900">{{ $complaint->tenant->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nomor Kamar</p>
                                <p class="text-base text-gray-900">{{ $complaint->room->room_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- DESKRIPSI KELUHAN --}}
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Deskripsi Detail</h4>
                        <p class="text-base text-gray-700 p-4 bg-gray-100 rounded-md whitespace-pre-line">{{ $complaint->description }}</p>
                    </div>

                    {{-- FOTO KELUHAN --}}
                    <div class="mb-6 border-t pt-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">
                            {{-- Hapus .count(), gunakan fungsi count() PHP bawaan --}}
                            Lampiran Foto Keluhan ({{ count($complaint->images ?? []) }}) 
                        </h4>
                        
                        {{-- Tambahkan ?? [] untuk mengatasi kasus null jika tidak ada gambar --}}
                        @if(empty($complaint->images)) 
                            <p class="text-gray-500 italic">Tidak ada foto yang dilampirkan pada keluhan ini.</p>
                        @else
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                {{-- $imagePath adalah string, bukan object --}}
                                @foreach($complaint->images as $imagePath) 
                                    @php
                                        $imageUrl = asset('storage/' . $imagePath);
                                    @endphp
                                    <a href="{{ $imageUrl }}" target="_blank" class="block group relative overflow-hidden rounded-lg shadow-md hover:shadow-lg transition duration-200">
                                        <img src="{{ $imageUrl }}" 
                                            alt="Foto Keluhan" 
                                            class="w-full h-32 object-cover transition-opacity duration-300 group-hover:opacity-80">
                                        {{-- Detail overlay tetap sama --}}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- RESPON/RESOLUSI --}}
                    @if($complaint->response)
                    <div class="mb-6 border-t pt-4">
                        <h4 class="text-lg font-semibold text-blue-700 mb-2">Respon</h4>
                        <div class="p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg shadow-sm">
                            <p class="text-base text-gray-800 whitespace-pre-line">{{ $complaint->response }}</p>
                            @if($complaint->resolved_date)
                            <p class="mt-2 text-xs text-gray-500">Diselesaikan pada: {{ $complaint->resolved_date->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('complaints.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-150">
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
</x-app-layout>