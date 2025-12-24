<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi - {{ $payment->invoice_number }}</title>
    <style>
        html, body { 
            background-color: transparent !important; 
            background: transparent !important;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
            margin: 0; 
            padding: 30px; /* Ruang agar shadow tidak terpotong saat di-screenshot */
        }

        .no-print { text-align: right; max-width: 550px; margin: 0 auto 10px auto; }
        .btn-print { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        
        .card { 
            background: #ffffff !important; 
            max-width: 550px; 
            margin: 0 auto; 
            border-radius: 24px !important; /* Sudut lebih bulat sesuai gambar */
            overflow: hidden; /* Memotong header biru agar ikut bulat */
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); 
            border: none; 
        }

        .header { background: #1e40af; color: white; padding: 35px 20px; text-align: center; border: none; }
        .header h1 { margin: 0; font-size: 26px; letter-spacing: 1px; font-weight: 800; }
        .header p { margin: 5px 0 0 0; font-size: 12px; opacity: 0.9; text-transform: uppercase; font-weight: 600; }
        
        .content { padding: 35px; }
        .inv-box { background: #eff6ff; border: 2px dashed #bfdbfe; border-radius: 12px; padding: 15px; text-align: center; margin-bottom: 25px; }
        .inv-label { font-size: 10px; color: #60a5fa; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .inv-number { font-size: 22px; color: #1e40af; font-weight: bold; font-family: 'Courier New', monospace; }
        
        .grid { display: flex; flex-wrap: wrap; margin-bottom: 20px; }
        .col { flex: 1; min-width: 200px; margin-bottom: 20px; }
        .label { font-size: 11px; color: #94a3b8; font-weight: bold; text-transform: uppercase; display: block; margin-bottom: 6px; }
        .value { font-size: 15px; font-weight: 800; color: #1e293b; }
        .room-val { font-size: 19px; color: #2563eb; }

        .total-box { background: #1e293b; color: white; padding: 25px; border-radius: 15px; display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
        .total-label { font-size: 13px; opacity: 0.8; font-weight: 600; }
        .total-amount { font-size: 26px; font-weight: 800; color: #60a5fa; }

        .footer-note { margin-top: 35px; border-top: 1px solid #f1f5f9; padding-top: 20px; text-align: center; font-size: 11px; color: #94a3b8; line-height: 1.5; }

        @media print { .no-print { display: none; } body { padding: 0; } .card { box-shadow: none; } }
    </style>
</head>
<body>

    <div class="card">
        <div class="header">
            <h1>SERRATA KOS</h1>
            <p>Kwitansi Pembayaran Resmi</p>
        </div>

        <div class="content">
            <div class="inv-box">
                <div class="inv-label">Nomor Invoice</div>
                <div class="inv-number">{{ $payment->invoice_number }}</div>
            </div>

            <div class="grid">
                <div class="col">
                    <span class="label">Nama Penghuni</span>
                    <span class="value">{{ $payment->tenant->name }}</span>
                    <br><br>
                    <span class="label">Nomor Kamar</span>
                    <span class="value room-val">{{ $payment->room->room_number }}</span>
                </div>
                <div class="col">
                    <span class="label">Periode</span>
                    <span class="value">{{ \Carbon\Carbon::parse($payment->period_month)->translatedFormat('F Y') }}</span>
                    <br><br>
                    <span class="label">Status</span>
                    <span style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold;">
                        LUNAS
                    </span>
                </div>
            </div>

            <div class="total-box">
                <span class="total-label">TOTAL DIBAYARKAN</span>
                <span class="total-amount">Rp {{ number_format($payment->total, 0, ',', '.') }}</span>
            </div>

            <div class="footer-note">
                Diterbitkan secara elektronik pada {{ date('d/m/Y H:i') }}<br>
                Serrata Kos
            </div>
        </div>
    </div>

</body>
</html>