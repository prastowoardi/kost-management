<x-guest-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">
                    Formulir Pendaftaran Penghuni
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Silahkan isi data diri Anda dengan lengkap untuk bergabung di Serrata Kost.
                </p>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <form action="{{ route('public.register.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pilih Kamar</label>
                                <select name="room_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Pilih Kamar Tersedia --</option>
                                    @foreach($availableRooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        Kamar {{ $room->room_number }} - {{ ucfirst($room->type) }} (Rp {{ number_format($room->price, 0, ',', '.') }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Masukkan nama lengkap sesuai KTP">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="contoh@mail.com">
                                    @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">No. WhatsApp (Aktif)</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="081234567xxx">
                                    @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">No. KTP / ID Card</label>
                                <input type="text" name="id_card" value="{{ old('id_card') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="16 digit nomor NIK">
                                @error('id_card')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Alamat Lengkap (Asal)</label>
                                <textarea name="address" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Alamat lengkap sesuai domisili">{{ old('address') }}</textarea>
                                @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Rencana Tanggal Masuk</label>
                                <input type="date" name="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}" required
                                    onclick="this.showPicker()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                                @error('entry_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kontak Darurat</label>
                                <textarea name="emergency_contact" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Nama & No. HP Orang Tua / Kerabat">{{ old('emergency_contact') }}</textarea>
                                @error('emergency_contact')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Upload Foto KTP / Foto Diri (Wajib)</label>
                                <input type="file" name="photo" accept="image/*" required
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <label class="block text-sm font-bold text-blue-800 mb-3">Metode Pembayaran Awal</label>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 bg-white border rounded-md cursor-pointer hover:border-blue-400">
                                    <input type="radio" name="payment_method" value="transfer" required class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900">Transfer Bank (Konfirmasi Otomatis)</span>
                                        <span class="block text-xs text-gray-500">Transfer ke Rekening Admin Serrata Kost</span>
                                    </span>
                                </label>

                                <label class="flex items-center p-3 bg-white border rounded-md cursor-pointer hover:border-blue-400">
                                    <input type="radio" name="payment_method" value="cash" class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900">Bayar Tunai di Lokasi</span>
                                        <span class="block text-xs text-gray-500">Datang langsung ke lokasi saat check-in</span>
                                    </span>
                                </label>
                            </div>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                                DAFTAR SEKARANG
                            </button>
                            <p class="mt-4 text-xs text-center text-gray-500">
                                Dengan mendaftar, Anda setuju dengan tata tertib yang berlaku di Serrata Kost.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>