<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kamar') }} - {{ $room->room_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('rooms.update', $room) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Kamar</label>
                                <input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('room_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipe Kamar</label>
                                <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="single" {{ old('type', $room->type) == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="double" {{ old('type', $room->type) == 'double' ? 'selected' : '' }}>Double</option>
                                    <option value="shared" {{ old('type', $room->type) == 'shared' ? 'selected' : '' }}>Shared</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Harga per Bulan (Rp)</label>
                                    <input type="number" name="price" value="{{ old('price', $room->price) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kapasitas (orang)</label>
                                    <input type="number" name="capacity" value="{{ old('capacity', $room->capacity) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                    <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ukuran (m²)</label>
                                <input type="number" step="0.01" name="size" value="{{ old('size', $room->size) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fasilitas Kamar</label>
                                <div class="mt-2 grid grid-cols-2 gap-2">
                                    @foreach($facilities as $facility)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                            {{ in_array($facility->id, $selectedFacilities) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                        <span class="ml-2 text-sm text-gray-700">{{ $facility->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $room->description) }}</textarea>
                            </div>

                            <!-- Existing Images -->
                            @php
                                $existingImages = is_string($room->images) ? json_decode($room->images, true) : $room->images;
                            @endphp
                            
                            @if($existingImages && count($existingImages) > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Kamar Saat Ini</label>
                                <div class="grid grid-cols-5 gap-3" id="existing-images-container">
                                    @foreach($existingImages as $index => $image)
                                    <div class="relative group" id="existing-image-{{ $index }}">
                                        <img src="{{ asset('storage/' . $image) }}" 
                                                alt="Foto {{ $index + 1 }}" 
                                                class="w-full h-24 object-cover rounded-lg border border-gray-300">
                                        <button type="button" 
                                                onclick="removeExistingImage({{ $index }}, '{{ $image }}')"
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition hover:bg-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                                <!-- Hidden inputs for images to keep -->
                                <div id="keep-images-container">
                                    @foreach($existingImages as $index => $image)
                                    <input type="hidden" name="keep_images[]" value="{{ $image }}" id="keep-image-{{ $index }}">
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- New Images Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Foto Baru (Opsional)
                                    @if($existingImages && count($existingImages) > 0)
                                    <span class="text-xs text-gray-500">(Maksimal total 5 foto termasuk yang sudah ada)</span>
                                    @else
                                    <span class="text-xs text-gray-500">(Maksimal 5 foto)</span>
                                    @endif
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                                    <input type="file" 
                                            name="new_images[]" 
                                            id="new-room-images" 
                                            accept="image/*" 
                                            multiple
                                            class="hidden">
                                    <label for="new-room-images" class="cursor-pointer">
                                        <div class="text-gray-600">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p class="mt-1 text-sm">Click untuk upload foto baru</p>
                                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG (Max 2MB per file)</p>
                                        </div>
                                    </label>
                                </div>
                                @error('new_images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <!-- New Images Preview -->
                                <div id="new-images-preview" class="grid grid-cols-5 gap-3 mt-4 hidden"></div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3">
                            <a href="{{ route('rooms.show', $room) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Update Kamar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Track existing images count
        let existingImagesCount = {{ $existingImages ? count($existingImages) : 0 }};
        
        // Remove existing image
        function removeExistingImage(index, imagePath) {
            if (confirm('Hapus foto ini?')) {
                document.getElementById('existing-image-' + index).remove();
                document.getElementById('keep-image-' + index).remove();
                existingImagesCount--;
            }
        }

        // Preview new images
        document.getElementById('new-room-images').addEventListener('change', function(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('new-images-preview');
            const maxTotal = 5;
            
            previewContainer.innerHTML = '';
            
            // Check total images limit
            const totalImages = existingImagesCount + files.length;
            if (totalImages > maxTotal) {
                alert(`Total maksimal ${maxTotal} foto! Saat ini ada ${existingImagesCount} foto, Anda bisa upload maksimal ${maxTotal - existingImagesCount} foto lagi.`);
                event.target.value = '';
                return;
            }
            
            if (files.length > 0) {
                previewContainer.classList.remove('hidden');
            }
            
            Array.from(files).forEach((file, index) => {
                // Check file size
                if (file.size > 2048000) {
                    alert(`File ${file.name} terlalu besar! Max 2MB per file.`);
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border border-gray-300">
                        <span class="absolute top-1 right-1 bg-green-600 text-white text-xs px-2 py-1 rounded">Baru</span>
                    `;
                    previewContainer.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        });
    </script>
</x-app-layout>