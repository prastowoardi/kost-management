<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Edit Kamar') }} - {{ $room->room_number }}
        </h2>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <form action="{{ route('rooms.update', $room) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="form-group">
                                <label for="room_number" class="form-label">Nomor Kamar</label>
                                <input type="text" id="room_number" name="room_number" value="{{ old('room_number', $room->room_number) }}" required
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('room_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" required class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                    <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="price" class="form-label">Harga per Bulan (Rp)</label>
                                <input type="number" id="price" name="price" value="{{ old('price', $room->price) }}" required
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>

                            <div class="form-group">
                                <label for="capacity" class="form-label">Kapasitas (orang)</label>
                                <input type="number" id="capacity" name="capacity" value="{{ old('capacity', $room->capacity) }}" required
                                    min="1"
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>

                            <div class="form-group">
                                <label for="type" class="form-label">Tipe Kamar</label>
                                <select id="type" name="type" required class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="singlenoac" {{ old('type', $room->type) == 'singlenoac' ? 'selected' : '' }}>Single Non AC</option>
                                    <option value="singleac" {{ old('type', $room->type) == 'singleac' ? 'selected' : '' }}>Single AC</option>
                                    <option value="shared" {{ old('type', $room->type) == 'shared' ? 'selected' : '' }}>Shared</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="size" class="form-label">Ukuran (m²)</label>
                                <input type="number" id="size" step="0.01" name="size" value="{{ old('size', $room->size) }}"
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>

                            <div class="form-group md:col-span-2">
                                <label class="form-label">Fasilitas Kamar</label>
                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($facilities as $facility)
                                    <label class="inline-flex items-center rounded-lg border border-stone-100 bg-stone-50/50 px-3 py-2 cursor-pointer transition hover:bg-stone-100">
                                        <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                            {{ in_array($facility->id, $selectedFacilities) ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-stone-300 text-brand-600 shadow-sm focus:border-brand-300 focus:ring focus:ring-brand-200">
                                        <span class="ml-2 text-sm text-stone-700">{{ $facility->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group md:col-span-2">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea id="description" name="description" rows="3"
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('description', $room->description) }}</textarea>
                            </div>

                            <!-- Existing Images -->
                            @php
                                $existingImages = is_string($room->images) ? json_decode($room->images, true) : $room->images;
                            @endphp

                            @if($existingImages && count($existingImages) > 0)
                            <div class="md:col-span-2">
                                <label class="form-label mb-2">Foto Kamar Saat Ini</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3" id="existing-images-container">
                                    @foreach($existingImages as $index => $image)
                                    <div class="relative group" id="existing-image-{{ $index }}">
                                        <img src="{{ Storage::url($image) }}"
                                                alt="Foto {{ $index + 1 }}"
                                                class="w-full h-24 object-cover rounded-xl border border-stone-200">
                                        <button type="button"
                                                data-remove-image="{{ $index }}"
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
                            <div class="form-group md:col-span-2">
                                <label class="form-label mb-1">
                                    Upload Foto Baru (Opsional)
                                    @if($existingImages && count($existingImages) > 0)
                                    <span class="text-xs text-stone-400">(Maksimal total 5 foto termasuk yang sudah ada)</span>
                                    @else
                                    <span class="text-xs text-stone-400">(Maksimal 5 foto)</span>
                                    @endif
                                </label>
                                <div class="border-2 border-dashed border-stone-200 rounded-2xl bg-stone-50/50 p-6 text-center transition hover:border-brand-400 hover:bg-brand-50/40">
                                    <input type="file"
                                            name="new_images[]"
                                            id="new-room-images"
                                            accept="image/*"
                                            multiple
                                            class="hidden">
                                    <label for="new-room-images" class="cursor-pointer">
                                        <div class="text-stone-600">
                                            <svg class="mx-auto h-12 w-12 text-stone-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p class="mt-1 text-sm font-medium">Click untuk upload foto baru</p>
                                            <p class="text-xs text-stone-500 mt-1">PNG, JPG, JPEG (Max 2MB per file)</p>
                                        </div>
                                    </label>
                                </div>
                                @error('new_images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <!-- New Images Preview -->
                                <div id="new-images-preview" data-existing-count="{{ $existingImages ? count($existingImages) : 0 }}" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 mt-4 hidden"></div>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                            <a href="{{ route('rooms.show', $room) }}" class="btn-secondary w-full sm:w-auto text-center">
                                Batal
                            </a>
                            <button type="submit" class="btn-primary w-full sm:w-auto">
                                Update Kamar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/pages/rooms-form.js')
    @endpush
</x-app-layout>
