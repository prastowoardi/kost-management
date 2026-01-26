<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Receipt - {{ $payment->invoice_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        html, body { 
            background-color: #f0f9f9; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; 
            padding: 40px 20px;
            color: #134e4a; /* Deep Teal */
        }

        .card { 
            background: #ffffff; 
            max-width: 480px; 
            margin: 0 auto; 
            /* border-radius: 32px;  */
            overflow: hidden; 
            box-shadow: 0 20px 40px -10px rgba(13, 148, 136, 0.1);
            position: relative;
        }

        .card::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(20, 184, 166, 0.08);
            border-radius: 50%;
        }

        .header { 
            padding: 45px 40px 20px 40px; 
            text-align: left; 
        }
        
        .brand-name { 
            font-size: 13px; 
            font-weight: 800; 
            color: #0d9488; /* Teal Accent */
            letter-spacing: 2px; 
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }

        .header h1 { 
            margin: 0; 
            font-size: 26px; 
            color: #134e4a;
            font-weight: 800; 
            letter-spacing: -0.5px;
        }

        .content { padding: 0 40px 40px 40px; }

        .status-badge {
            display: inline-block;
            background: #f0fdf4;
            color: #16a34a;
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 12px;
            border: 1px solid #dcfce7;
        }
        
        .divider {
            height: 1px;
            background: #f1f5f9;
            margin: 30px 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .info-item { margin-bottom: 5px; }

        .label { 
            font-size: 11px; 
            color: #94a3b8; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            display: block; 
            margin-bottom: 4px; 
        }

        .value { 
            font-size: 15px; 
            font-weight: 700; 
            color: #334155; 
        }

        .amount-card { 
            background: #f0fdfa; 
            padding: 24px; 
            border-radius: 24px; 
            margin-top: 30px;
            border: 1px solid #ccfbf1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .total-label { 
            font-size: 12px; 
            color: #115e59; 
            font-weight: 700;
            display: block;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .total-amount { 
            font-size: 32px;
            font-weight: 800; 
            color: #115e59; 
            margin-top: 5px;
            align-items: baseline;
        }

        .total-amount::before {
            content: "Rp";
            font-size: 14px;
            color: #115e59;
            margin-right: 8px;
            font-weight: 700;
        }

        .total-box { 
            background: #064e3b; 
            color: white; 
            padding: 25px 30px; 
            border-radius: 24px; 
            
            display: flex; 
            flex-direction: column;
            justify-content: center; 
            align-items: center;
            
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(6, 78, 59, 0.2);
            text-align: center;
        }

        .footer { 
            text-align: center; 
            margin-top: 30px; 
            font-size: 11px; 
            color: #94a3b8;
            font-weight: 500;
        }

        .invoice-no {
            font-family: 'Courier New', Courier, monospace;
            background: #f8fafc;
            padding: 3px 8px;
            border-radius: 6px;
            color: #64748b;
            font-size: 13px;
        }

        @media print { 
            body { background: white; padding: 0; }
            .card { box-shadow: none; border: 1px solid #f1f5f9; }
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="header">
            <span class="brand-name">Serrata Kost</span>
            <h1>E-Kwitansi</h1>
            <div class="status-badge">● Lunas</div>
        </div>

        <div class="content">
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Penyewa</span>
                    <span class="value">{{ $payment->tenant->name }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Unit Kamar</span>
                    <span class="value" style="color: #0d9488;">No. {{ $payment->room->room_number }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Periode Pembayaran</span>
                    <span class="value">{{ \Carbon\Carbon::parse($payment->period_month)->translatedFormat('F Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">No. Invoice</span>
                    <span class="value invoice-no">#{{ $payment->invoice_number }}</span>
                </div>
            </div>

            <div class="amount-card">
                <span class="total-label">Total Diterima</span>
                <div class="total-amount">{{ number_format($payment->total, 0, ',', '.') }}</div>
            </div>

            <div class="divider"></div>

            <div style="text-align: center;">
                <p style="font-size: 10px; color: #64748b; margin: 0; line-height: 1.6;">
                    Generated by System {{ date('d M Y, H:i') }}
                </p>
            </div>
        </div>
    </div>

</body>
</html>