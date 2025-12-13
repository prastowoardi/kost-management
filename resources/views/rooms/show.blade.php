<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Kamar') }} {{ $room->room_number }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('rooms.edit', $room) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    ✏️ Edit
                </a>
                <a href="{{ route('rooms.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Images Gallery -->
                    @if($room->images && count($room->images) > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Foto Kamar</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($room->images as $image)
                                <img src="{{ asset('storage/' . $image) }}" 
                                        alt="Foto Kamar" 
                                        class="w-full h-48 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-75 transition"
                                        onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Room Details -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kamar</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Nomor Kamar</p>
                                    <p class="font-semibold text-gray-900">{{ $room->room_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Tipe</p>
                                    <p class="font-semibold text-gray-900">{{ ucfirst($room->type) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Harga per Bulan</p>
                                    <p class="font-semibold text-gray-900">Rp {{ number_format($room->price, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Kapasitas</p>
                                    <p class="font-semibold text-gray-900">{{ $room->capacity }} orang</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Ukuran</p>
                                    <p class="font-semibold text-gray-900">{{ $room->size ? $room->size . ' m²' : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($room->status == 'available') bg-green-100 text-green-800
                                        @elseif($room->status == 'occupied') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($room->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($room->description)
                            <div class="mt-4 pt-4 border-t">
                                <p class="text-sm text-gray-600 mb-2">Deskripsi</p>
                                <p class="text-gray-700">{{ $room->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Facilities -->
                    @if($room->facilities->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Fasilitas</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($room->facilities as $facility)
                                <div class="flex items-center space-x-2 text-sm">
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">{{ $facility->name }}</span>
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
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Penghuni Saat Ini</h3>
                            @if($room->activeTenant)
                            <div class="flex items-center space-x-3 mb-4">
                                @if($room->activeTenant->photo)
                                <img src="{{ asset('storage/' . $room->activeTenant->photo) }}" class="h-12 w-12 rounded-full object-cover" alt="{{ $room->activeTenant->name }}">
                                @else
                                <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-600 font-bold">{{ substr($room->activeTenant->name, 0, 1) }}</span>
                                </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $room->activeTenant->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $room->activeTenant->email }}</p>
                                </div>
                            </div>
                            <a href="{{ route('tenants.show', $room->activeTenant) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                Lihat Detail →
                            </a>
                            @else
                            <p class="text-gray-500 text-center py-4">Kamar kosong</p>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Pembayaran</h3>
                            @if($room->payments->count() > 0)
                            <div class="space-y-3">
                                @foreach($room->payments->take(5) as $payment)
                                <div class="border-b pb-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $payment->period_month->format('M Y') }}</p>
                                            <p class="text-xs text-gray-500">{{ $payment->payment_date->format('d M Y') }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $payment->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900 mt-1">Rp {{ number_format($payment->total, 0, ',', '.') }}</p>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-gray-500 text-center py-4 text-sm">Belum ada pembayaran</p>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Complaints -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Keluhan Terbaru</h3>
                            @if($room->complaints->count() > 0)
                            <div class="space-y-3">
                                @foreach($room->complaints->take(3) as $complaint)
                                <div class="border-b pb-2">
                                    <p class="text-sm font-medium text-gray-900">{{ $complaint->title }}</p>
                                    <div class="flex justify-between items-center mt-1">
                                        <p class="text-xs text-gray-500">{{ $complaint->created_at->format('d M Y') }}</p>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $complaint->status == 'resolved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($complaint->status) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-gray-500 text-center py-4 text-sm">Belum ada keluhan</p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
        <div class="max-w-4xl max-h-full">
            <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-[90vh] object-contain rounded-lg">
        </div>
    </div>

    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }
    </script>
</x-app-layout>