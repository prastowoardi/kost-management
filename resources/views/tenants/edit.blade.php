<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Edit Penghuni') }}
        </h2>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tenants.update', $tenant) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2 form-group">
                                <label for="room_id" class="form-label">Kamar</label>
                                <select name="room_id" id="room_id" required class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">Pilih Kamar</option>
                                    @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id', $tenant->room_id) == $room->id ? 'selected' : '' }}>
                                        Kamar {{ $room->room_number }} - {{ ucfirst($room->type) }} (Rp {{ number_format($room->price, 0, ',', '.') }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 form-group">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $tenant->name) }}" required
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('name')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $tenant->email) }}" required
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('email')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">No. Telepon</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $tenant->phone) }}" required
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('phone')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 form-group">
                                <label for="id_card" class="form-label">No. KTP/ID Card</label>
                                <input type="text" id="id_card" name="id_card" value="{{ old('id_card', $tenant->id_card) }}" required
                                    class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('id_card')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 form-group">
                                <label for="address" class="form-label">Alamat Lengkap</label>
                                <textarea name="address" id="address" rows="3" required class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('address', $tenant->address) }}</textarea>
                                @error('address')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="entry_date" class="form-label">Tanggal Masuk</label>
                                <input type="date" id="entry_date" name="entry_date" value="{{ old('entry_date', $tenant->entry_date->format('Y-m-d')) }}" required
                                    class="mt-1 block w-full cursor-pointer rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('entry_date')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="exit_date" class="form-label">Tanggal Keluar (Optional)</label>
                                <input type="date" id="exit_date" name="exit_date" value="{{ old('exit_date', $tenant->exit_date?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full cursor-pointer rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('exit_date')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 form-group">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" required class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="active" {{ old('status', $tenant->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $tenant->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5 border-t border-stone-100 pt-5">
                                <div class="md:col-span-2">
                                    <p class="text-xs font-bold uppercase tracking-wider text-stone-400">
                                        Kontak Darurat (Optional)
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label for="emergency_contact_name" class="form-label">Nama Kontak Darurat</label>
                                    <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                        value="{{ old('emergency_contact_name', $tenant->emergency_contact_name) }}"
                                        class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                        placeholder="Ayah/Ibu">
                                    @error('emergency_contact_name')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="emergency_contact_phone" class="form-label">No. HP Darurat</label>
                                    <input type="text" id="emergency_contact_phone" name="emergency_contact_phone"
                                        value="{{ old('emergency_contact_phone', $tenant->emergency_contact_phone) }}"
                                        class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                        placeholder="0812345xxxx">
                                    @error('emergency_contact_phone')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="md:col-span-2 form-group">
                                <label for="photo" class="form-label mb-2">Foto</label>
                                @if($tenant->photo)
                                <div class="mb-3">
                                    <img src="{{ Storage::url($tenant->photo) }}" alt="{{ $tenant->name }}" class="h-32 w-32 rounded-full object-cover border-2 border-brand-100">
                                    <p class="text-sm text-stone-500 mt-1">Foto saat ini</p>
                                </div>
                                @endif
                                <input type="file" id="photo" name="photo" accept="image/*"
                                    class="block w-full text-sm text-stone-500 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                                <p class="text-sm text-stone-500">Upload foto baru jika ingin mengganti</p>
                                @error('photo')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-stone-100 pt-5">
                            <a href="{{ route('tenants.index') }}" class="btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn-primary">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/pages/tenants-form.js')
    @endpush
</x-app-layout>
