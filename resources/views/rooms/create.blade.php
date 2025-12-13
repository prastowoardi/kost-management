{{-- resources/views/rooms/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Kamar Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('rooms.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Kamar</label>
                                <input type="text" name="room_number" value="{{ old('room_number') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('room_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipe Kamar</label>
                                <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Tipe</option>
                                    <option value="single" {{ old('type') == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="double" {{ old('type') == 'double' ? 'selected' : '' }}>Double</option>
                                    <option value="shared" {{ old('type') == 'shared' ? 'selected' : '' }}>Shared</option>
                                </select>
                                @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Harga per Bulan (Rp)</label>
                                    <input type="number" name="price" value="{{ old('price') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kapasitas (orang)</label>
                                    <input type="number" name="capacity" value="{{ old('capacity', 1) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('capacity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ukuran (m²)</label>
                                <input type="number" step="0.01" name="size" value="{{ old('size') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('size')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fasilitas Kamar</label>
                                <div class="mt-2 grid grid-cols-2 gap-2">
                                    @foreach($facilities as $facility)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                        <span class="ml-2 text-sm text-gray-700">{{ $facility->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                                @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Multiple Images Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Foto Kamar (Maksimal 5 foto)
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                                    <input type="file" 
                                           name="images[]" 
                                           id="room-images" 
                                           accept="image/*" 
                                           multiple
                                           class="hidden"
                                           onchange="previewRoomImages(event)">
                                    <label for="room-images" class="cursor-pointer">
                                        <div class="text-gray-600">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p class="mt-1 text-sm">Click untuk upload foto kamar</p>
                                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG (Max 5 foto, masing-masing max 2MB)</p>
                                        </div>
                                    </label>
                                </div>
                                @error('images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <!-- Preview Container -->
                                <div id="room-preview-container" class="grid grid-cols-5 gap-4 mt-4 hidden"></div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3">
                            <a href="{{ route('rooms.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewRoomImages(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('room-preview-container');
            
            previewContainer.innerHTML = '';
            
            if (files.length > 5) {
                alert('Maksimal 5 foto!');
                event.target.value = '';
                return;
            }
            
            if (files.length > 0) {
                previewContainer.classList.remove('hidden');
            }
            
            Array.from(files).forEach((file, index) => {
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
                        <span class="absolute top-1 right-1 bg-blue-600 text-white text-xs px-2 py-1 rounded">${index + 1}</span>
                    `;
                    previewContainer.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    </script>
</x-app-layout>