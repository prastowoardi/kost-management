<div class="max-w-4xl mx-auto p-10">
    <h2 class="text-2xl font-bold mb-6">History Kwitansi</h2>
    <table class="w-full bg-white rounded-2xl overflow-hidden shadow-lg">
        <thead class="bg-teal-600 text-white">
            <tr>
                <th class="p-4 text-left">Tanggal</th>
                <th class="p-4 text-left">Invoice</th>
                <th class="p-4 text-left">Penyewa</th>
                <th class="p-4 text-left">Total</th>
                <th class="p-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($history as $item)
            <tr class="border-b">
                <td class="p-4">{{ $item->created_at->format('d/m/Y') }}</td>
                <td class="p-4">#{{ $item->invoice_number }}</td>
                <td class="p-4">{{ $item->tenant_name }}</td>
                <td class="p-4 text-teal-700 font-bold">Rp {{ number_format($item->total_amount) }}</td>
                <td class="p-4 text-center">
                    <a href="{{ route('admin.receipt.print', $item->id) }}" class="text-teal-600 font-bold">Cetak Ulang</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>