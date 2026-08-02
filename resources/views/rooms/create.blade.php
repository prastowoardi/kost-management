{{-- resources/views/rooms/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Tambah Kamar Baru') }}
        </h2>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <form action="{{ route('rooms.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="form-group">
                                <label for="room_number" class="form-label">Nomor Kamar</label>
                                <input type="text" id="room_number" name="room_number" value="{{ old('room_number') }}" required
                                    placeholder="Contoh: A101"
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('room_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="size" class="form-label">Ukuran (m²)</label>
                                <input type="number" id="size" step="0.01" name="size" value="{{ old('size') }}"
                                    placeholder="Contoh: 3x3"
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('size')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="price" class="form-label">Harga per Bulan (Rp)</label>
                                <input type="number" id="price" name="price" value="{{ old('price') }}" required
                                    placeholder="Contoh: 750000"
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="capacity" class="form-label">Kapasitas (orang)</label>
                                <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 1) }}" required
                                    min="1"
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('capacity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group md:col-span-2">
                                <label for="type" class="form-label">Tipe Kamar</label>
                                <select id="type" name="type" required class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Pilih Tipe</option>
                                    <option value="singlenoac" {{ old('type') == 'singlenoac' ? 'selected' : '' }}>Single Non AC</option>
                                    <option value="singleac" {{ old('type') == 'singleac' ? 'selected' : '' }}>Single AC</option>
                                    <option value="shared" {{ old('type') == 'shared' ? 'selected' : '' }}>Shared</option>
                                </select>
                                @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group md:col-span-2">
                                <label class="form-label">Fasilitas Kamar</label>
                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($facilities as $facility)
                                    <label class="inline-flex items-center rounded-lg border border-stone-100 bg-stone-50/50 px-3 py-2 cursor-pointer transition hover:bg-stone-100">
                                        <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                            class="h-4 w-4 rounded border-stone-300 text-brand-600 shadow-sm focus:border-brand-300 focus:ring focus:ring-brand-200">
                                        <span class="ml-2 text-sm text-stone-700">{{ $facility->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group md:col-span-2">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea id="description" name="description" rows="3"
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                    placeholder="Deskripsi singkat kamar">{{ old('description') }}</textarea>
                                @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Multiple Images Upload -->
                            <div class="form-group md:col-span-2">
                                <label class="form-label mb-1">
                                    Foto Kamar (Maksimal 5 foto)
                                </label>
                                <div class="border-2 border-dashed border-stone-200 rounded-2xl bg-stone-50/50 p-6 text-center transition hover:border-brand-400 hover:bg-brand-50/40">
                                    <input type="file"
                                           name="images[]"
                                           id="room-images"
                                           accept="image/*"
                                           multiple
                                           class="hidden"
                                           onchange="previewRoomImages(event)">
                                    <label for="room-images" class="cursor-pointer">
                                        <div class="text-stone-600">
                                            <svg class="mx-auto h-12 w-12 text-stone-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p class="mt-1 text-sm font-medium">Click untuk upload foto kamar</p>
                                            <p class="text-xs text-stone-500 mt-1">PNG, JPG, JPEG (Max 5 foto, masing-masing max 2MB)</p>
                                        </div>
                                    </label>
                                </div>
                                @error('images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <!-- Preview Container -->
                                <div id="room-preview-container" class="grid grid-cols-3 sm:grid-cols-5 gap-4 mt-4 hidden"></div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-3">
                            <a href="{{ route('rooms.index') }}" class="btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn-primary">
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
