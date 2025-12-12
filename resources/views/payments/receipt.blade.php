<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi Pembayaran - {{ $payment->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            font-size: 14px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 12px;
        }
        .invoice-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            color: #2563eb;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            padding: 5px 0;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        .payment-table th {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .payment-table td {
            border: 1px solid #d1d5db;
            padding: 12px;
        }
        .total-row {
            background-color: #dbeafe;
            font-weight: bold;
            font-size: 16px;
        }
        .signature-section {
            margin-top: 60px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }
        .signature-line {
            margin-top: 80px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #d1d5db;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Serrata Kos</h1>
        <p>Jl. Contoh No. 123, Jakarta 12345 | Telp: (021) 1234-5678 | Email: info@kos.com</p>
    </div>

    <div class="invoice-title">
        KWITANSI PEMBAYARAN
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">No. Invoice</div>
            <div class="info-value">: {{ $payment->invoice_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal</div>
            <div class="info-value">: {{ $payment->payment_date->format('d F Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status</div>
            <div class="info-value">: 
                <span class="status-badge {{ $payment->status == 'paid' ? 'status-paid' : 'status-pending' }}">
                        {{ strtoupper($payment->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Nama Penghuni</div>
            <div class="info-value">: {{ $payment->tenant->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nomor Kamar</div>
            <div class="info-value">: {{ $payment->room->room_number }}</div>
        </div>
        <!-- <div class="info-row">
            <div class="info-label">Email</div>
            <div class="info-value">: {{ $payment->tenant->email }}</div>
        </div> -->
        <div class="info-row">
            <div class="info-label">Telepon</div>
            <div class="info-value">: {{ $payment->tenant->phone }}</div>
        </div>
    </div>

    <table class="payment-table">
        <thead>
            <tr>
                <th width="60%">Keterangan</th>
                <th width="40%" style="text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Pembayaran Sewa Kamar {{ $payment->room->room_number }}</strong><br>
                    <small>Periode: {{ $payment->period_month->format('F Y') }}</small>
                </td>
                <td style="text-align: right;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
            </tr>
            @if($payment->late_fee > 0)
            <tr>
                <td>Denda Keterlambatan</td>
                <td style="text-align: right;">Rp {{ number_format($payment->late_fee, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>TOTAL PEMBAYARAN</td>
                <td style="text-align: right;">Rp {{ number_format($payment->total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Metode Pembayaran</div>
            <div class="info-value">: {{ ucfirst(str_replace('-', ' ', $payment->payment_method ?? '-')) }}</div>
        </div>
        @if($payment->notes)
        <div class="info-row">
            <div class="info-label">Catatan</div>
            <div class="info-value">: {{ $payment->notes }}</div>
        </div>
        @endif
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p>Penghuni</p>
            <div class="signature-line">
                {{ $payment->tenant->name }}
            </div>
        </div>
        <div class="signature-box">
            <p>Penerima</p>
            <div class="signature-line">
                Admin Kos
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Kwitansi ini sah dan diproses oleh sistem.</p>
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>