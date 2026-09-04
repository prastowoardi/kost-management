<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Edit Fasilitas') }}
        </h2>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <form action="{{ route('facilities.update', $facility->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Nama Fasilitas --}}
                            <div class="form-group">
                                <label for="name" class="form-label">Nama Fasilitas</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $facility->name) }}" required
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                    placeholder="Contoh: AC, Wi-Fi, Parkir">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tipe Fasilitas --}}
                            <div class="form-group">
                                <label for="type" class="form-label">Tipe Fasilitas</label>
                                <select id="type" name="type" required
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Pilih Tipe</option>
                                    <option value="room" {{ old('type', $facility->type) == 'room' ? 'selected' : '' }}>Fasilitas Kamar</option>
                                    <option value="common" {{ old('type', $facility->type) == 'common' ? 'selected' : '' }}>Fasilitas Umum</option>
                                </select>
                                @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jumlah & Kondisi --}}
                            <div class="form-group">
                                <label for="quantity" class="form-label">Jumlah</label>
                                <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $facility->quantity) }}" required min="1"
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="condition" class="form-label">Kondisi</label>
                                <select id="condition" name="condition" required
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="good" {{ old('condition', $facility->condition) == 'good' ? 'selected' : '' }}>Baik</option>
                                    <option value="fair" {{ old('condition', $facility->condition) == 'fair' ? 'selected' : '' }}>Cukup</option>
                                    <option value="poor" {{ old('condition', $facility->condition) == 'poor' ? 'selected' : '' }}>Buruk</option>
                                </select>
                                @error('condition')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="form-group md:col-span-2">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea id="description" name="description" rows="3"
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                    placeholder="Deskripsi detail fasilitas">{{ old('description', $facility->description) }}</textarea>
                                @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="mt-8 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                            <a href="{{ route('facilities.index') }}" class="btn-secondary w-full sm:w-auto text-center">
                                Batal
                            </a>
                            <button type="submit" class="btn-primary w-full sm:w-auto">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
