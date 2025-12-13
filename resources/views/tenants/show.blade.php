<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Penghuni') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('tenants.edit', $tenant) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    ✏️ Edit
                </a>
                <a href="{{ route('tenants.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
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
                    
                    <!-- Personal Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-6">
                                <div class="flex items-center space-x-4">
                                    @if($tenant->photo)
                                    <img src="{{ asset('storage/' . $tenant->photo) }}" 
                                         alt="{{ $tenant->name }}" 
                                         class="h-24 w-24 rounded-full object-cover border-4 border-blue-100">
                                    @else
                                    <div class="h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center border-4 border-blue-100">
                                        <span class="text-gray-600 font-bold text-3xl">{{ substr($tenant->name, 0, 1) }}</span>
                                    </div>
                                    @endif
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">{{ $tenant->name }}</h3>
                                        <p class="text-gray-600">{{ $tenant->email }}</p>
                                        <span class="mt-2 px-3 py-1 inline-flex text-xs font-semibold rounded-full 
                                            {{ $tenant->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($tenant->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6 border-t pt-6">
                                <div>
                                    <p class="text-sm text-gray-600">No. Telepon</p>
                                    <p class="font-semibold text-gray-900">{{ $tenant->phone }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">No. KTP/ID</p>
                                    <p class="font-semibold text-gray-900">{{ $tenant->id_card }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-sm text-gray-600">Alamat</p>
                                    <p class="font-semibold text-gray-900">{{ $tenant->address }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Tanggal Masuk</p>
                                    <p class="font-semibold text-gray-900">{{ $tenant->entry_date->format('d F Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Tanggal Keluar</p>
                                    <p class="font-semibold text-gray-900">{{ $tenant->exit_date ? $tenant->exit_date->format('d F Y') : '-' }}</p>
                                </div>
                                @if($tenant->emergency_contact)
                                <div class="col-span-2">
                                    <p class="text-sm text-gray-600">Kontak Darurat</p>
                                    <p class="font-semibold text-gray-900">{{ $tenant->emergency_contact }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Payment History -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Pembayaran</h3>
                            @if($tenant->payments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($tenant->payments->take(10) as $payment)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $payment->period_month->format('M Y') }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">Rp {{ number_format($payment->total, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    {{ $payment->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-gray-500 text-center py-8">Belum ada riwayat pembayaran</p>
                            @endif
                        </div>
                    </div>

                    <!-- Complaints History -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Keluhan</h3>
                            @if($tenant->complaints->count() > 0)
                            <div class="space-y-4">
                                @foreach($tenant->complaints as $complaint)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-semibold text-gray-900">{{ $complaint->title }}</h4>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $complaint->status == 'resolved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($complaint->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $complaint->description }}</p>
                                    <div class="flex justify-between items-center text-xs text-gray-500">
                                        <span>{{ $complaint->created_at->format('d M Y') }}</span>
                                        <span class="px-2 py-1 bg-gray-100 rounded">{{ ucfirst($complaint->category) }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-gray-500 text-center py-8">Belum ada keluhan</p>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    
                    <!-- Room Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kamar</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-600">Nomor Kamar</p>
                                    <p class="text-xl font-bold text-blue-600">{{ $tenant->room->room_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Tipe</p>
                                    <p class="font-semibold text-gray-900">{{ ucfirst($tenant->room->type) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Harga Sewa</p>
                                    <p class="font-semibold text-gray-900">Rp {{ number_format($tenant->room->price, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Ukuran</p>
                                    <p class="font-semibold text-gray-900">{{ $tenant->room->size ? $tenant->room->size . ' m²' : '-' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('rooms.show', $tenant->room) }}" class="mt-4 block text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Lihat Detail Kamar
                            </a>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Pembayaran</span>
                                    <span class="font-semibold text-gray-900">{{ $tenant->payments->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Pengeluaran</span>
                                    <span class="font-semibold text-green-600">Rp {{ number_format($tenant->payments->sum('total'), 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Keluhan</span>
                                    <span class="font-semibold text-gray-900">{{ $tenant->complaints->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Lama Tinggal</span>
                                    <span class="font-semibold text-gray-900">
                                        {{ $tenant->entry_date->diffInDays($tenant->exit_date ?? now()) }} hari
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>