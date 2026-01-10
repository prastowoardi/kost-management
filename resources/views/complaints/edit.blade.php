<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update Status Keluhan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    
                    <!-- Read Only Information -->
                    <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Keluhan</h3>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600">Penghuni</p>
                                <p class="font-semibold text-gray-900">{{ $complaint->tenant->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Kamar</p>
                                <p class="font-semibold text-gray-900">{{ $complaint->room->room_number }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-600">Judul Keluhan</p>
                                <p class="font-semibold text-gray-900">{{ $complaint->title }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-600">Deskripsi</p>
                                <p class="text-gray-700">{{ $complaint->description }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Kategori</p>
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-gray-200 text-gray-800">
                                    {{ ucfirst($complaint->category) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Prioritas</p>
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                                    @if($complaint->priority == 'high') bg-red-100 text-red-800
                                    @elseif($complaint->priority == 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($complaint->priority) }}
                                </span>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-600 mb-2">Tanggal Dibuat</p>
                                <p class="text-gray-700">{{ $complaint->created_at->format('d F Y H:i') }}</p>
                            </div>
                        </div>

                        <!-- Images -->
                        @if($complaint->images && count($complaint->images) > 0)
                        <div class="mt-6">
                            <p class="text-sm text-gray-600 mb-3">Foto Keluhan</p>
                            <div class="grid grid-cols-5 gap-4">
                                @foreach($complaint->images as $image)
                                <img src="{{ asset('storage/' . $image) }}" 
                                        alt="Foto Keluhan" 
                                        class="w-full h-24 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-75"
                                        onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Editable Status Form -->
                    <form action="{{ route('complaints.update', $complaint) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Hidden fields untuk data yang tidak diubah -->
                        <input type="hidden" name="tenant_id" value="{{ $complaint->tenant_id }}">
                        <input type="hidden" name="title" value="{{ $complaint->title }}">
                        <input type="hidden" name="description" value="{{ $complaint->description }}">
                        <input type="hidden" name="category" value="{{ $complaint->category }}">
                        <input type="hidden" name="priority" value="{{ $complaint->priority }}">
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status Keluhan</label>
                                <select name="status" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="open" {{ old('status', $complaint->status) == 'open' ? 'selected' : '' }}>Open - Belum Ditangani</option>
                                    <option value="in_progress" {{ old('status', $complaint->status) == 'in_progress' ? 'selected' : '' }}>In Progress - Sedang Ditangani</option>
                                    <option value="resolved" {{ old('status', $complaint->status) == 'resolved' ? 'selected' : '' }}>Resolved - Sudah Selesai</option>
                                    <option value="closed" {{ old('status', $complaint->status) == 'closed' ? 'selected' : '' }}>Closed - Ditutup</option>
                                </select>
                                @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggapan / Response</label>
                                <textarea name="response" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Tulis tanggapan untuk keluhan ini...">{{ old('response', $complaint->response) }}</textarea>
                                @error('response')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                                <input type="date" name="resolved_date" value="{{ old('resolved_date', $complaint->resolved_date?->format('Y-m-d')) }}"
                                    onclick="this.showPicker()"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                                <p class="mt-1 text-sm text-gray-500">Isi jika keluhan sudah resolved/closed</p>
                                @error('resolved_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-3 border-t pt-6">
                            <a href="{{ route('complaints.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Update Status
                            </button>
                        </div>
                    </form>

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