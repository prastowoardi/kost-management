<x-app-layout>
    <x-slot name="hideNavigation">true</x-slot>

    <div class="min-h-screen bg-gradient-to-b from-white via-brand-50/40 to-stone-100 py-10 sm:py-16">
        <div class="mx-auto w-full max-w-3xl px-4 sm:px-6">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-soft">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-stone-900 sm:text-4xl">
                    Registration <span class="text-brand-600">Form</span>
                </h1>
                <p class="mt-3 text-sm text-stone-500 sm:text-base">
                    Silahkan isi data diri Anda dengan lengkap untuk registrasi di Serrata Kost.
                </p>
            </div>

            <div class="card p-6 sm:p-10">
                <form action="{{ route('public.register.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="register-form">
                    @csrf

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="form-label">Pilih Kamar</label>
                            <select name="room_id" required class="mt-1.5 block w-full cursor-pointer">
                                <option value="">-- Pilih Kamar Tersedia --</option>
                                @foreach($availableRooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    Kamar {{ $room->room_number }}
                                </option>
                                @endforeach
                            </select>
                            @error('room_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="mt-1.5 block w-full" placeholder="Masukkan nama lengkap sesuai KTP">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="mt-1.5 block w-full" placeholder="contoh@mail.com">
                            @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">WhatsApp Aktif</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" maxlength="13" required
                                class="mt-1.5 block w-full" placeholder="081234567xxx">
                            @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">No. KTP / ID Card</label>
                            <input type="text" name="id_card" value="{{ old('id_card') }}" maxlength="16" required
                                class="mt-1.5 block w-full" placeholder="16 digit nomor NIK">
                            @error('id_card')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">Rencana Tanggal Masuk</label>
                            <input type="date" name="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}" required
                                class="mt-1.5 block w-full cursor-pointer">
                            @error('entry_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Alamat Lengkap (Asal)</label>
                            <textarea name="address" rows="3" required class="mt-1.5 block w-full" placeholder="Alamat lengkap sesuai domisili">{{ old('address') }}</textarea>
                            @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-stone-400">Kontak Darurat</h3>
                        </div>

                        <div>
                            <label class="form-label">Nama Kontak Darurat</label>
                            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                                class="mt-1.5 block w-full"
                                placeholder="Contoh: Ayah / Ibu">
                            @error('emergency_contact_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">No. HP Darurat</label>
                            <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" maxlength="13"
                                class="mt-1.5 block w-full"
                                placeholder="0812xxxxxx">
                            @error('emergency_contact_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Upload Foto KTP <span class="font-bold text-brand-600">(Wajib)</span></label>
                            <input type="file" name="photo" accept="image/*" required
                                class="mt-1.5 block w-full text-sm text-stone-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                            @error('photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-50/80 to-white p-5 sm:p-6">
                        <h3 class="mb-5 flex items-center gap-2 text-base font-extrabold text-stone-900">
                            <svg class="h-5 w-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h2m-2 4h10a2 2 0 002-2V8a2 2 0 00-2-2H7a2 2 0 00-2 2v9a2 2 0 002 2z"></path></svg>
                            Metode Pembayaran
                        </h3>

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-stone-200 bg-white p-3.5 shadow-sm transition hover:border-brand-300 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/50 has-[:checked]:ring-1 has-[:checked]:ring-brand-500">
                                <input type="radio" name="payment_method" value="transfer" class="h-4 w-4 text-brand-600 focus:ring-brand-500" required {{ old('payment_method') == 'transfer' ? 'checked' : '' }}>
                                <span class="text-sm font-semibold text-stone-800">Transfer Bank</span>
                            </label>

                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-stone-200 bg-white p-3.5 shadow-sm transition hover:border-brand-300 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/50 has-[:checked]:ring-1 has-[:checked]:ring-brand-500">
                                <input type="radio" name="payment_method" value="cash" class="h-4 w-4 text-brand-600 focus:ring-brand-500" required {{ old('payment_method') == 'cash' ? 'checked' : '' }}>
                                <span class="text-sm font-semibold text-stone-800">Bayar Tunai</span>
                            </label>
                        </div>

                        <div id="transfer-details-container" class="hidden space-y-4 animate-fade-in">
                            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="rounded-2xl border border-stone-100 bg-white p-5 shadow-soft">
                                    <div class="mb-4 flex items-center justify-between gap-3">
                                        <p class="text-xs font-bold text-stone-800">Bank Mandiri</p>
                                        <img src="https://upload.wikimedia.org/wikipedia/id/thumb/f/fa/Bank_Mandiri_logo.svg/1200px-Bank_Mandiri_logo.svg.png" class="h-5">
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <p id="acc_mandiri" class="tabular text-lg font-black text-brand-600 leading-none">1360014406059</p>
                                        <button type="button" data-copy="acc_mandiri" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-brand-100 bg-brand-50 px-2.5 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-100 active:scale-95">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                            Salin
                                        </button>
                                    </div>
                                    <p class="mt-2 text-[11px] text-stone-500">A/N Prastowo Ardi Widigdo</p>
                                </div>

                                <div class="rounded-2xl border border-stone-100 bg-white p-5 shadow-soft">
                                    <div class="mb-4 flex items-center justify-between gap-3">
                                        <p class="text-xs font-bold text-stone-800">Bank Jago</p>
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c0/Logo-jago.svg" class="h-5">
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <p id="acc_jago" class="tabular text-lg font-black text-orange-500 leading-none">109781903718</p>
                                        <button type="button" data-copy="acc_jago" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-orange-100 bg-orange-50 px-2.5 py-1.5 text-xs font-semibold text-orange-600 transition hover:bg-orange-100 active:scale-95">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                            Salin
                                        </button>
                                    </div>
                                    <p class="mt-2 text-[11px] text-stone-500">A/N Prastowo Ardi Widigdo</p>
                                </div>
                            </div>

                            <div class="mt-1">
                                <label class="block text-xs font-bold uppercase tracking-wide text-brand-700">Unggah Bukti Transfer</label>
                                <input type="file" name="receipt_file" id="receipt_input" accept="image/*"
                                    class="mt-2 block w-full text-sm text-stone-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-50">
                            </div>
                        </div>

                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="btn btn-primary btn-lg w-full">
                            DAFTAR SEKARANG
                        </button>
                        <p class="mt-4 text-center text-xs text-stone-400">
                            Dengan mendaftar, Anda setuju dengan tata tertib yang berlaku di Serrata Kost.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/pages/public-register.js')
    @endpush
</x-app-layout>
