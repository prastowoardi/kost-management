<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Fasilitas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('facilities.update', $facility->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            {{-- Nama Fasilitas --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Fasilitas</label>
                                <input type="text" name="name" value="{{ old('name', $facility->name) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-black"
                                    placeholder="Contoh: AC, Wi-Fi, Parkir">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tipe Fasilitas --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipe Fasilitas</label>
                                <select name="type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-black">
                                    <option value="">Pilih Tipe</option>
                                    <option value="room" {{ old('type', $facility->type) == 'room' ? 'selected' : '' }}>Fasilitas Kamar</option>
                                    <option value="common" {{ old('type', $facility->type) == 'common' ? 'selected' : '' }}>Fasilitas Umum</option>
                                </select>
                                @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jumlah & Kondisi --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                                    <input type="number" name="quantity" value="{{ old('quantity', $facility->quantity) }}" required min="1"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-black">
                                    @error('quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kondisi</label>
                                    <select name="condition" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-black">
                                        <option value="good" {{ old('condition', $facility->condition) == 'good' ? 'selected' : '' }}>Baik</option>
                                        <option value="fair" {{ old('condition', $facility->condition) == 'fair' ? 'selected' : '' }}>Cukup</option>
                                        <option value="poor" {{ old('condition', $facility->condition) == 'poor' ? 'selected' : '' }}>Buruk</option>
                                    </select>
                                    @error('condition')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea name="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-black"
                                    placeholder="Deskripsi detail fasilitas">{{ old('description', $facility->description) }}</textarea>
                                @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="mt-6 flex items-center justify-end gap-3">
                            <a href="{{ route('facilities.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
