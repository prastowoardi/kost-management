<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pembayaran - {{ date('d M Y') }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .total-row { font-weight: bold; background-color: #e0f7fa; }
        .text-right { text-align: right; }
        .status-paid { color: green; }
        .status-pending { color: orange; }
        .status-overdue { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pembayaran Kos</h2>
        <p>Periode: {{ request('start_date') ?? 'Awal' }} sampai {{ request('end_date') ?? 'Akhir' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Penghuni</th>
                <th>Kamar</th>
                <th>Periode</th>
                <th>Tgl Bayar</th>
                <th class="text-right">Jumlah (Total)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->invoice_number ?? 'N/A' }}</td>
                <td>{{ $payment->tenant->name }}</td>
                <td>{{ $payment->room->room_number }}</td>
                <td>{{ \Carbon\Carbon::parse($payment->period_month)->format('M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                <td class="text-right">Rp {{ number_format($payment->total, 0, ',', '.') }}</td>
                <td>
                    <span class="status-{{ $payment->status }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL KESELURUHAN:</td>
                <td class="text-right">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>