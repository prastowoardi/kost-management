<div class="overflow-x-auto rounded-xl border border-stone-100 shadow-card">
    <table class="min-w-max w-full">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Penghuni</th>
                <th>Kamar</th>
                <th>Periode</th>
                <th>Tanggal Bayar</th>
                <th class="text-right">Jumlah (Total)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td class="font-medium text-stone-900 tabular">
                    {{ $payment->invoice_number ?? 'N/A' }}
                </td>
                <td>
                    <span class="font-medium text-stone-900">{{ $payment->tenant->name }}</span>
                </td>
                <td>
                    {{ $payment->room->room_number }}
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($payment->period_month)->format('M Y') }}
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                </td>
                <td class="text-right font-semibold text-stone-900 tabular">
                    Rp {{ number_format($payment->total, 0, ',', '.') }}
                </td>
                <td>
                    <span class="badge
                        @if($payment->status == 'paid') badge-success
                        @elseif($payment->status == 'pending') badge-warning
                        @elseif($payment->status == 'overdue') badge-danger
                        @else badge-neutral @endif">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
