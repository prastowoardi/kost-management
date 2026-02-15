<x-app-layout>
    <x-slot name="hideNavigation">true</x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">
                    Registration Form
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Silahkan isi data diri Anda dengan lengkap untuk registrasi di Serrata Kost.
                </p>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <form action="{{ route('public.register.store') }}" 
                        method="POST" 
                        enctype="multipart/form-data" 
                        onsubmit="showLoading('Sedang memproses pendaftaran...')">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pilih Kamar</label>
                                <select name="room_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                                    <label class="block text-sm font-medium text-gray-700">WhatsApp Aktif</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" maxlength="13" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="081234567xxx">
                                    @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">No. KTP / ID Card</label>
                                <input type="text" name="id_card" value="{{ old('id_card') }}" maxlength="16" required
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Kontak Darurat</label>
                                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                        placeholder="Contoh: Ayah / Ibu">
                                    @error('emergency_contact_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">No. HP Darurat</label>
                                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" maxlength="13"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                        placeholder="0812xxxxxx">
                                    @error('emergency_contact_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Upload Foto KTP (Wajib)</label>
                                <input type="file" name="photo" accept="image/*" required
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h3 class="text-lg font-bold text-blue-800 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2 2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Metode Pembayaran
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <label class="flex items-center p-3 bg-white border rounded-md cursor-pointer hover:border-blue-400">
                                    <input type="radio" name="payment_method" value="transfer" onclick="toggleTransferDetails(true)" class="text-blue-600 focus:ring-blue-500" required {{ old('payment_method') == 'transfer' ? 'checked' : '' }}>
                                    <span class="ml-3 font-medium text-gray-900 text-sm">Transfer Bank</span>
                                </label>

                                <label class="flex items-center p-3 bg-white border rounded-md cursor-pointer hover:border-blue-400">
                                    <input type="radio" name="payment_method" value="cash" onclick="toggleTransferDetails(false)" class="text-blue-600 focus:ring-blue-500" required {{ old('payment_method') == 'cash' ? 'checked' : '' }}>
                                    <span class="ml-3 font-medium text-gray-900 text-sm">Bayar Tunai</span>
                                </label>
                            </div>

                            <div id="transfer-details-container" class="hidden space-y-4">
                                <div class="bg-white p-5 rounded-2xl border border-blue-200 shadow-inner">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-4 tracking-widest text-center">Tujuan Transfer</p>
                                    
                                    <div class="space-y-6">
                                        <div class="flex items-start justify-between border-b border-blue-50 pb-4">
                                            <div class="w-full">
                                                <p class="text-xs font-bold text-slate-800 mb-1">Bank Mandiri</p>
                                                <div class="flex items-center space-x-2">
                                                    <p id="acc_mandiri" class="text-lg font-black text-blue-600 leading-none">1360014406059</p>
                                                    <button type="button" onclick="copyText('acc_mandiri')" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition active:scale-95">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                    </button>
                                                </div>
                                                <p class="text-[10px] text-slate-500 mt-2">A/N Prastowo Ardi Widigdo</p>
                                            </div>
                                            <img src="https://upload.wikimedia.org/wikipedia/id/thumb/f/fa/Bank_Mandiri_logo.svg/1200px-Bank_Mandiri_logo.svg.png" class="h-5">
                                        </div>

                                        <div class="flex items-start justify-between">
                                            <div class="w-full">
                                                <p class="text-xs font-bold text-slate-800 mb-1">Bank Jago</p>
                                                <div class="flex items-center space-x-2">
                                                    <p id="acc_jago" class="text-lg font-black text-orange-500 leading-none">109781903718</p>
                                                    <button type="button" onclick="copyText('acc_jago')" class="p-1.5 bg-orange-50 text-orange-600 rounded-md hover:bg-orange-100 transition active:scale-95">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                    </button>
                                                </div>
                                                <p class="text-[10px] text-slate-500 mt-2">A/N Prastowo Ardi Widigdo</p>
                                            </div>
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c0/Logo-jago.svg" class="h-5">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 p-1">
                                    <label class="block text-xs font-bold text-blue-600 uppercase mb-2 ml-1">Unggah Bukti Transfer</label>
                                    <input type="file" name="receipt_file" id="receipt_input" accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-50">
                                </div>
                            </div>
                        </div>
                        <div id="transfer-info" class="hidden space-y-4 animate-fadeIn">
                            <div class="bg-white p-5 rounded-2xl border border-blue-200 shadow-inner">
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-4 tracking-widest text-center">Tujuan Transfer</p>
                                
                                <div class="space-y-6">
                                    <div class="flex items-start justify-between border-b border-blue-50 pb-4">
                                        <div class="w-full">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <p class="text-xs font-bold text-slate-800">Bank Mandiri</p>
                                                <img src="https://upload.wikimedia.org/wikipedia/id/thumb/f/fa/Bank_Mandiri_logo.svg/1200px-Bank_Mandiri_logo.svg.png"
                                                        class="h-3 md:hidden">
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <p id="acc_mandiri" class="text-lg font-black text-blue-600 leading-none">1360014406059</p>
                                                <button type="button" onclick="copyText('acc_mandiri')" class="p-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition active:scale-95 flex items-center border border-blue-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                </button>
                                            </div>
                                            <p class="text-[10px] text-slate-500 mt-2">A/N Prastowo Ardi Widigdo</p>
                                        </div>
                                        <img src="https://upload.wikimedia.org/wikipedia/id/thumb/f/fa/Bank_Mandiri_logo.svg/1200px-Bank_Mandiri_logo.svg.png" class="hidden md:block h-5">
                                    </div>

                                    <div class="flex items-start justify-between">
                                        <div class="w-full">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <p class="text-xs font-bold text-slate-800">Bank Jago</p>
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c0/Logo-jago.svg" 
                                                        class="h-3 block md:hidden">
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <p id="acc_jago" class="text-lg font-black text-orange-500 leading-none">109781903718</p> <button type="button" onclick="copyText('acc_jago')" class="p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition active:scale-95 flex items-center border border-orange-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                </button>
                                            </div>
                                            <p class="text-[10px] text-slate-500 mt-2">A/N Prastowo Ardi Widigdo</p>
                                        </div>
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c0/Logo-jago.svg" class="hidden md:block h-5">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4" id="receipt-upload" class="hidden animate-fadeIn">
                                <label class="block text-xs font-bold text-blue-600 uppercase mb-2 ml-1">Unggah Bukti Transfer</label>
                                <input type="file" name="receipt_file" id="receipt_input" accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-50">
                            </div>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 transition duration-150">
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
</x-app-layout>

<script>
    function toggleTransferDetails(isTransfer) {
        const container = document.getElementById('transfer-details-container');
        const inputField = document.getElementById('receipt_input');

        if(isTransfer) {
            container.classList.remove('hidden');
            inputField.setAttribute('required', 'required');
        } else {
            container.classList.add('hidden');
            inputField.removeAttribute('required');
            inputField.value = ""; 
        }
    }

    function copyText(elementId) {
        const textToCopy = document.getElementById(elementId).innerText;
        
        navigator.clipboard.writeText(textToCopy).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil disalin!',
                text: textToCopy + ' telah siap ditempel.',
                showConfirmButton: false,
                timer: 1200,
                toast: true,
                position: 'top-end'
            });
        }).catch(err => {
            console.error('Gagal menyalin: ', err);
        });
    }
</script>