<!-- resources/views/admin/receipt/manual_form.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serrata Kost - Create Receipt</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f0f9f9] p-6 md:p-12">

    <div class="max-w-xl mx-auto bg-white p-8 rounded-[2.5rem] shadow-xl border border-teal-100">
        <div class="mb-8">
            <span class="text-teal-600 font-extrabold tracking-widest text-xs uppercase">Serrata Kost</span>
            <h2 class="text-3xl font-black text-slate-800 mt-2">Buat Kwitansi Baru</h2>
            <p class="text-slate-400 text-sm mt-1">Data akan disimpan ke history dan lanjut ke cetak.</p>
        </div>
        
        <form action="{{ route('admin.receipt.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase ml-1 mb-2">Nama Penyewa</label>
                <input type="text" name="tenant_name" required placeholder="Contoh: Sarah Ananda" 
                    class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-teal-500 outline-none transition">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase ml-1 mb-2">No. Kamar</label>
                    <input type="text" name="room_number" required placeholder="01" 
                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-teal-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase ml-1 mb-2">Periode</label>
                    <input type="month" name="period" required value="{{ date('Y-m') }}"
                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-teal-500 outline-none transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase ml-1 mb-2">Nomor Invoice</label>
                <input type="text" name="invoice_number" required 
                    value="{{ $newInvoiceNumber }}" 
                    readonly
                    class="w-full px-5 py-4 rounded-2xl bg-slate-100 border-none text-slate-500 cursor-not-allowed outline-none transition">
                <small class="text-teal-600 ml-1 italic">*Nomor dibuat otomatis oleh sistem</small>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase ml-1 mb-2">Total Diterima (Rp)</label>
                <input type="number" name="total_amount" required placeholder="1500000"
                    class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-teal-500 outline-none transition">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-teal-600 text-white font-bold py-5 rounded-[1.5rem] shadow-lg shadow-teal-200 hover:bg-teal-700 hover:-translate-y-1 transition-all duration-300">
                    Simpan & Generate Kwitansi 🚀
                </button>
            </div>
        </form>
    </div>

</body>
</html>