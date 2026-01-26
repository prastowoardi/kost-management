<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 16px;
            color: #64748b;
            font-weight: normal;
        }
        
        .period {
            text-align: center;
            font-size: 14px;
            color: #475569;
            margin-bottom: 20px;
        }
        
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        
        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            border: 2px solid #e5e7eb;
            text-align: center;
        }
        
        .summary-card.income {
            background-color: #f0fdf4;
            border-color: #22c55e;
        }
        
        .summary-card.expense {
            background-color: #fef2f2;
            border-color: #ef4444;
        }
        
        .summary-card.balance {
            background-color: #eff6ff;
            border-color: #3b82f6;
        }
        
        .summary-card h3 {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .summary-card .amount {
            font-size: 20px;
            font-weight: bold;
        }
        
        .summary-card.income .amount { color: #16a34a; }
        .summary-card.expense .amount { color: #dc2626; }
        .summary-card.balance .amount { color: #2563eb; }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 12px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border: 1px solid #cbd5e1;
            font-size: 11px;
        }
        
        table tbody td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        table tfoot td {
            padding: 10px;
            font-weight: bold;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .text-blue { color: #2563eb; }
        
        .category-breakdown {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .category-col {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
            vertical-align: top;
        }
        
        .category-item {
            padding: 8px;
            margin-bottom: 8px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            background-color: #f9fafb;
        }
        
        .category-item .name {
            font-weight: bold;
            font-size: 11px;
            color: #1e293b;
            margin-bottom: 3px;
        }
        
        .category-item .amount {
            font-size: 13px;
            font-weight: bold;
        }
        
        .category-item .percentage {
            font-size: 10px;
            color: #64748b;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Serrata Kost</h1>
        <h2>Laporan Keuangan</h2>
    </div>
    
    <!-- Period -->
    <div class="period">
        <strong>Periode: {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</strong>
    </div>
    
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card income">
            <h3>Total Pemasukan</h3>
            <div class="amount">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card expense">
            <h3>Total Pengeluaran</h3>
            <div class="amount">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card balance">
            <h3>Saldo</h3>
            <div class="amount">Rp {{ number_format($balance, 0, ',', '.') }}</div>
        </div>
    </div>
    
    <!-- Income & Expense by Category -->
    <div class="section">
        <div class="section-title">Ringkasan per Kategori</div>
        <div class="category-breakdown">
            <!-- Income Categories -->
            <div class="category-col">
                <h4 style="margin-bottom: 10px; color: #16a34a;">Pemasukan per Kategori</h4>
                @if($incomeByCategory->count() > 0)
                    @foreach($incomeByCategory as $item)
                    <div class="category-item">
                        <div class="name">{{ $item->category }}</div>
                        <div class="amount text-green">Rp {{ number_format($item->total, 0, ',', '.') }}</div>
                        <div class="percentage">{{ $totalIncome > 0 ? number_format($item->total / $totalIncome * 100, 1) : 0 }}%</div>
                    </div>
                    @endforeach
                @else
                    <p style="color: #94a3b8; font-style: italic;">Tidak ada data</p>
                @endif
            </div>
            
            <!-- Expense Categories -->
            <div class="category-col">
                <h4 style="margin-bottom: 10px; color: #dc2626;">Pengeluaran per Kategori</h4>
                @if($expenseByCategory->count() > 0)
                    @foreach($expenseByCategory as $item)
                    <div class="category-item">
                        <div class="name">{{ $item->category }}</div>
                        <div class="amount text-red">Rp {{ number_format($item->total, 0, ',', '.') }}</div>
                        <div class="percentage">{{ $totalExpense > 0 ? number_format($item->total / $totalExpense * 100, 1) : 0 }}%</div>
                    </div>
                    @endforeach
                @else
                    <p style="color: #94a3b8; font-style: italic;">Tidak ada data</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Detailed Transactions -->
    <div class="section">
        <div class="section-title">Detail Transaksi</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 20%;">Kategori</th>
                    <th style="width: 35%;">Deskripsi</th>
                    <th style="width: 15%;" class="text-right">Pemasukan</th>
                    <th style="width: 15%;" class="text-right">Pengeluaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($finances as $finance)
                <tr>
                    <td>{{ $finance->transaction_date->format('d/m/Y') }}</td>
                    <td>{{ $finance->category }}</td>
                    <td>{{ $finance->description }}</td>
                    <td class="text-right text-green">
                        {{ $finance->type == 'income' ? 'Rp ' . number_format($finance->amount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right text-red">
                        {{ $finance->type == 'expense' ? 'Rp ' . number_format($finance->amount, 0, ',', '.') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #94a3b8; font-style: italic; padding: 20px;">
                        Tidak ada transaksi pada periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right">TOTAL:</td>
                    <td class="text-right text-green">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                    <td class="text-right text-red">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">SALDO:</td>
                    <td colspan="2" class="text-right text-blue" style="font-size: 14px;">
                        Rp {{ number_format($balance, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <!-- Monthly Trend (if exists) -->
    @if(isset($monthlyTrend) && count($monthlyTrend) > 0)
    <div class="section page-break">
        <div class="section-title">Tren 6 Bulan Terakhir</div>
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th class="text-right">Pemasukan</th>
                    <th class="text-right">Pengeluaran</th>
                    <th class="text-right">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyTrend as $trend)
                <tr>
                    <td><strong>{{ $trend['month'] }}</strong></td>
                    <td class="text-right text-green">Rp {{ number_format($trend['income'], 0, ',', '.') }}</td>
                    <td class="text-right text-red">Rp {{ number_format($trend['expense'], 0, ',', '.') }}</td>
                    <td class="text-right text-blue">
                        <strong>Rp {{ number_format($trend['income'] - $trend['expense'], 0, ',', '.') }}</strong>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }}</p>
        <p>Laporan Keuangan Kos Management System</p>
    </div>
</body>
</html>