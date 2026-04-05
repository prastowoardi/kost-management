<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Receipt - {{ $payment->invoice_number }}</title>
    
    <!-- Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        /* --- DESIGN ASLI --- */
        body { 
            background-color: #f0f9f9; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 20px auto; 
            padding: 0;
            color: #134e4a;
        }

        /* Area yang akan di-render & dilihat di layar */
        #receipt-content {
            padding: 40px 20px;
            background-color: #f0f9f9; /* Memastikan background teal ikut saat di-screenshot */
        }

        .card { 
            background: #ffffff; 
            width: 100%;
            max-width: 480px; 
            margin: 0 auto; 
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

        .header { padding: 45px 40px 20px 40px; text-align: left; }
        .brand-name { font-size: 13px; font-weight: 800; color: #0d9488; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 8px; display: block; }
        .header h1 { margin: 0; font-size: 26px; color: #134e4a; font-weight: 800; letter-spacing: -0.5px; }
        .content { padding: 0 40px 40px 40px; }
        .status-badge { display: inline-block; background: #f0fdf4; color: #16a34a; padding: 6px 16px; border-radius: 100px; font-size: 12px; font-weight: 700; margin-top: 12px; border: 1px solid #dcfce7; }
        .divider { height: 1px; background: #f1f5f9; margin: 30px 0; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px; }
        .value { font-size: 15px; font-weight: 700; color: #334155; }
        
        .amount-card { 
            background: #f0fdfa; 
            padding: 24px; 
            border-radius: 24px; 
            margin-top: 30px;
            border: 1px solid #ccfbf1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .total-label { font-size: 12px; color: #115e59; font-weight: 700; text-transform: uppercase; }
        .total-amount { font-size: 32px; font-weight: 800; color: #115e59; margin-top: 5px; }
        .total-amount::before { content: "Rp"; font-size: 14px; margin-right: 8px; }
        .invoice-no { font-family: 'Courier New', Courier, monospace; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #64748b; font-size: 13px; }

        /* --- AREA TOMBOL --- */
        .action-container {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            background: white;
            border-top: 1px solid #e2e8f0;
        }

        .btn-group { display: flex; gap: 10px; }
        
        .btn {
            padding: 12px 20px; 
            border-radius: 12px; 
            font-weight: 700; 
            cursor: pointer; 
            border: none;
            display: flex; 
            align-items: center; 
            gap: 8px;
            font-size: 14px;
        }
        .btn-pdf { background: #0d9488; color: white; }
        .btn-image { background: #134e4a; color: white; }
        
        @media print { 
            .action-container { display: none; } 
            .card {
                margin: 0 auto;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    <!-- Tampilan Kwitansi (Sama persis dengan designmu) -->
    <div id="receipt-content">
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
                        <span class="value">{{ $payment->tenant_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Unit Kamar</span>
                        <span class="value" style="color: #0d9488;">No. {{ $payment->room_number }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Periode</span>
                        <span class="value">{{ \Carbon\Carbon::parse($payment->period)->translatedFormat('F Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">No. Invoice</span>
                        <span class="value invoice-no">#{{ $payment->invoice_number }}</span>
                    </div>
                </div>

                <div class="amount-card">
                    <span class="total-label">Total Diterima</span>
                    <div class="total-amount">{{ number_format($payment->total_amount, 0, ',', '.') }}</div>
                </div>

                <div class="divider"></div>

                <div style="text-align: center; font-size: 10px; color: #64748b;">
                    <p style="margin: 0;">Generated by System {{ $payment->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Navigasi & Download -->
    <div class="action-container">
        <div class="btn-group">
            <button onclick="downloadPDF()" class="btn btn-pdf">📄 PDF</button>
            <button onclick="downloadImage()" class="btn btn-image">🖼️ Image</button>
        </div>
        <a href="{{ route('admin.receipt.create') }}" style="color: #0d9488; text-decoration: none; font-size: 13px; font-weight: 600;">← Buat Baru</a>
    </div>

    <script>
        const element = document.querySelector('.card');
        const filename = "Kwitansi-{{ $payment->invoice_number }}";

        function downloadPDF() {
            const opt = {
                margin: 0,
                filename: filename + '.pdf',
                image: { type: 'jpeg', quality: 1 },
                html2canvas: { scale: 3, useCORS: true, backgroundColor: '#ffffff' },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }

        function downloadImage() {
            html2canvas(element, { scale: 4, backgroundColor: '#ffffff', useCORS: true }).then(canvas => {
                const link = document.createElement('a');
                link.download = filename + '.jpg';
                link.href = canvas.toDataURL("image/jpeg", 1.0);
                link.click();
            });
        }
    </script>
</body>
</html>