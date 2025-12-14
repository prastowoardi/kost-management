<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $payment->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 2px solid #2563eb;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .receipt-header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .receipt-header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .receipt-header .subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .receipt-body {
            padding: 30px;
        }
        
        .info-section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: 600;
            color: #64748b;
            min-width: 150px;
        }
        
        .info-value {
            color: #1e293b;
            font-weight: 500;
            text-align: right;
            flex: 1;
        }
        
        .invoice-number {
            background: #eff6ff;
            border: 2px dashed #2563eb;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .invoice-number .label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .invoice-number .number {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        
        .payment-details {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .payment-details h3 {
            color: #1e293b;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            color: #475569;
        }
        
        .detail-value {
            font-weight: 600;
            color: #1e293b;
        }
        
        .total-section {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .total-label {
            font-size: 18px;
            font-weight: 600;
        }
        
        .total-amount {
            font-size: 32px;
            font-weight: bold;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .status-paid {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .notes {
            background: #fefce8;
            border-left: 4px solid #eab308;
            padding: 15px;
            margin-top: 25px;
            border-radius: 4px;
        }
        
        .notes strong {
            color: #854d0e;
            display: block;
            margin-bottom: 5px;
        }
        
        .notes p {
            color: #713f12;
            font-size: 13px;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }
        
        .signature-box {
            text-align: center;
            width: 40%;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            margin-top: 80px;
            padding-top: 10px;
            font-weight: 600;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .print-button:hover {
            background: #1e40af;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .receipt-container {
                border: none;
                box-shadow: none;
            }
            
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">🖨️ Cetak Kwitansi</button>
    
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <h1>🏠 Serrata Kos</h1>
            <div class="subtitle">KWITANSI PEMBAYARAN</div>
        </div>
        
        <!-- Body -->
        <div class="receipt-body">
            <!-- Invoice Number -->
            <div class="invoice-number">
                <div class="label">Nomor Invoice</div>
                <div class="number">{{ $payment->invoice_number }}</div>
            </div>
            
            <!-- Tenant Information -->
            <div class="info-section">
                <h3 style="color: #1e293b; margin-bottom: 15px; font-size: 16px;">Informasi Penghuni</h3>
                <div class="info-row">
                    <div class="info-label">Nama Lengkap</div>
                    <div class="info-value">{{ $payment->tenant->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $payment->tenant->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">No. Telepon</div>
                    <div class="info-value">{{ $payment->tenant->phone ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nomor Kamar</div>
                    <div class="info-value">{{ $payment->room->room_number }}</div>
                </div>
            </div>
            
            <!-- Payment Details -->
            <div class="payment-details">
                <h3>Rincian Pembayaran</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Periode</div>
                    <div class="detail-value">{{ $payment->period_month->format('F Y') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Tanggal Pembayaran</div>
                    <div class="detail-value">{{ $payment->payment_date->format('d F Y') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Metode Pembayaran</div>
                    <div class="detail-value">{{ ucfirst($payment->payment_method ?? 'Cash') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="status-badge {{ $payment->status == 'paid' ? 'status-paid' : 'status-pending' }}">
                            {{ $payment->status == 'paid' ? 'Lunas' : 'Pending' }}
                        </span>
                    </div>
                </div>
                
                @if($payment->notes)
                <div class="detail-row">
                    <div class="detail-label">Catatan</div>
                    <div class="detail-value">{{ $payment->notes }}</div>
                </div>
                @endif
            </div>
            
            <!-- Total Amount -->
            <div class="total-section">
                <div class="total-row">
                    <div class="total-label">TOTAL PEMBAYARAN</div>
                    <div class="total-amount">Rp {{ number_format($payment->total, 0, ',', '.') }}</div>
                </div>
            </div>
            
            <!-- Notes -->
            @if($payment->status == 'paid')
            <div class="notes">
                <strong>✓ Pembayaran Lunas</strong>
                <p>Terima kasih atas pembayaran Anda. Kwitansi ini merupakan bukti pembayaran yang sah.</p>
            </div>
            @else
            <div class="notes">
                <strong>⚠️ Status Pending</strong>
                <p>Pembayaran masih dalam proses verifikasi. Silakan hubungi pengelola kos untuk informasi lebih lanjut.</p>
            </div>
            @endif
            
            <!-- Signature -->
            <div class="signature-section">
                <div class="signature-box">
                    <div>Penerima,</div>
                    <div class="signature-line">
                        Ibu Kos
                    </div>
                </div>
                <div class="signature-box">
                    <div>Penghuni,</div>
                    <div class="signature-line">
                        {{ $payment->tenant->name }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }}</p>
            <p>Kwitansi ini dibuat secara elektronik dan sah tanpa tanda tangan basah</p>
        </div>
    </div>
</body>
</html>