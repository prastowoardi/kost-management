<div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penghuni</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kamar</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Bayar</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah (Total)</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @foreach($payments as $payment)
            <tr>
                <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">
                    {{ $payment->invoice_number ?? 'N/A' }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                    {{ $payment->tenant->name }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                    {{ $payment->room->room_number }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($payment->period_month)->format('M Y') }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                </td>
                <td class="px-4 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap text-right">
                    Rp {{ number_format($payment->total, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full
                        @if($payment->status == 'paid') bg-green-100 text-green-800
                        @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>