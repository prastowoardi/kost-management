<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Tambah Keluhan Baru') }}
        </h2>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="page-container">
            <div class="mx-auto max-w-3xl">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Penghuni</label>
                                    <select name="tenant_id" required class="mt-1 block w-full">
                                        <option value="">Pilih Penghuni</option>
                                        @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                            {{ $tenant->name }} - Kamar {{ $tenant->room->room_number }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('tenant_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Judul Keluhan</label>
                                    <input type="text" name="title" value="{{ old('title') }}" required
                                        class="mt-1 block w-full"
                                        placeholder="Contoh: AC tidak dingin">
                                    @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Deskripsi Keluhan</label>
                                    <textarea name="description" rows="4" required class="mt-1 block w-full" placeholder="Jelaskan detail keluhan...">{{ old('description') }}</textarea>
                                    @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Kategori</label>
                                    <select name="category" required class="mt-1 block w-full">
                                        <option value="">Pilih Kategori</option>
                                        <option value="facility" {{ old('category') == 'facility' ? 'selected' : '' }}>Fasilitas</option>
                                        <option value="cleanliness" {{ old('category') == 'cleanliness' ? 'selected' : '' }}>Kebersihan</option>
                                        <option value="security" {{ old('category') == 'security' ? 'selected' : '' }}>Keamanan</option>
                                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Prioritas</label>
                                    <select name="priority" required class="mt-1 block w-full">
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Sedang</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                                    </select>
                                    @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="form-label mb-2">
                                        Foto Keluhan (Maksimal 5 foto)
                                    </label>
                                    <div class="rounded-2xl border-2 border-dashed border-stone-200 p-6 text-center transition hover:border-brand-400">
                                        <input type="file"
                                               name="images[]"
                                               id="images"
                                               accept="image/*"
                                               multiple
                                               class="hidden">
                                        <label for="images" class="cursor-pointer">
                                            <div class="text-stone-600">
                                                <svg class="mx-auto h-12 w-12 text-stone-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <p class="mt-1 font-medium text-stone-700">Klik untuk upload foto</p>
                                                <p class="text-xs text-stone-400">PNG, JPG, JPEG (Max 5 foto, masing-masing max 2MB)</p>
                                            </div>
                                        </label>
                                    </div>
                                    @error('images')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    @error('images.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                    <!-- Preview Container -->
                                    <div id="preview-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mt-4 hidden"></div>
                                </div>
                            </div>

                            <div class="mt-8 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 border-t border-stone-100 pt-6">
                                <a href="{{ route('complaints.index') }}" class="btn-secondary w-full sm:w-auto text-center">
                                    Batal
                                </a>
                                <button type="submit" class="btn-primary w-full sm:w-auto">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/pages/complaints-form.js')
    @endpush
</x-app-layout>
