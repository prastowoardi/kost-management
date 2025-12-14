<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $defaultEndDate = now()->format('Y-m-d');
        $defaultStartDate = now()->subDays(30)->format('Y-m-d');

        $startDate = $request->input('start_date')
            ? $request->input('start_date')
            : $defaultStartDate;
        
        $endDate = $request->input('end_date')
            ? $request->input('end_date')
            : $defaultEndDate;

        $query = Finance::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $query->where('transaction_date', '>=', $startDate)
                ->where('transaction_date', '<=', $endDate);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $finances = $query->orderBy('transaction_date', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(20);

        $totalIncome = Finance::income()
            ->where('transaction_date', '>=', $startDate)
            ->where('transaction_date', '<=', $endDate)
            ->sum('amount');

        $totalExpense = Finance::expense()
            ->where('transaction_date', '>=', $startDate)
            ->where('transaction_date', '<=', $endDate)
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

    
        $incomeCategories = Finance::income()->distinct()->pluck('category');
        $expenseCategories = Finance::expense()->distinct()->pluck('category');

        return view('finances.index', compact(
            'finances',
            'totalIncome',
            'totalExpense',
            'balance',
            'incomeCategories',
            'expenseCategories',
            'startDate',
            'endDate'  
        ));
    }

    public function create()
    {
        $incomeCategories = [
            'Pembayaran Sewa',
            'Deposit',
            'Denda Keterlambatan',
            'Biaya Listrik',
            'Biaya Air',
            'Lainnya'
        ];

        $expenseCategories = [
            'Gaji Karyawan',
            'Listrik',
            'Air',
            'Internet',
            'Perawatan Bangunan',
            'Perbaikan Fasilitas',
            'Kebersihan',
            'Keamanan',
            'Pajak',
            'Lainnya'
        ];

        return view('finances.create', compact('incomeCategories', 'expenseCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'payment_id' => 'nullable|exists:payments,id', 
        ]);

        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file'] = $request->file('receipt_file')->store('finances', 'public');
        }
        
        $validated['amount'] = (int) str_replace(['.', ','], '', $validated['amount']);

        Finance::create($validated);

        return redirect()->route('finances.index')
                        ->with('success', 'Transaksi keuangan berhasil ditambahkan');
    }

    public function show(Finance $finance)
    {
        return view('finances.show', compact('finance'));
    }

    public function edit(Finance $finance)
    {
        $incomeCategories = [
            'Pembayaran Sewa',
            'Deposit',
            'Denda Keterlambatan',
            'Biaya Listrik',
            'Biaya Air',
            'Lainnya'
        ];

        $expenseCategories = [
            'Gaji Karyawan',
            'Listrik',
            'Air',
            'Internet',
            'Perawatan Bangunan',
            'Perbaikan Fasilitas',
            'Kebersihan',
            'Keamanan',
            'Pajak',
            'Lainnya'
        ];

        return view('finances.edit', compact('finance', 'incomeCategories', 'expenseCategories'));
    }

    public function update(Request $request, Finance $finance)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);
        
        $validated['amount'] = (int) str_replace(['.', ','], '', $validated['amount']);

        if ($request->hasFile('receipt_file')) {
            if ($finance->receipt_file) {
                Storage::disk('public')->delete($finance->receipt_file);
            }
            $validated['receipt_file'] = $request->file('receipt_file')->store('finances', 'public');
        }
        
        $finance->update($validated);

        return redirect()->route('finances.index')
                        ->with('success', 'Transaksi keuangan berhasil diupdate');
    }

    public function destroy(Finance $finance)
    {
        if ($finance->receipt_file) {
            Storage::disk('public')->delete($finance->receipt_file);
        }

        $finance->delete();

        return redirect()->route('finances.index')
                        ->with('success', 'Transaksi keuangan berhasil dihapus');
    }

    public function report(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $finances = Finance::whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();
        
        $totalIncome = Finance::where('type', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
        
        $totalExpense = Finance::where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
        
        $balance = $totalIncome - $totalExpense;
        
        $incomeByCategory = Finance::where('type', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        $expenseByCategory = Finance::where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        // Monthly Trend
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $income = Finance::where('type', 'income')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $expense = Finance::where('type', 'expense')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $monthlyTrend[] = [
                'month' => $date->format('F Y'),
                'income' => $income,
                'expense' => $expense,
            ];
        }
        
        $data = compact(
            'finances', 
            'month', 
            'year', 
            'totalIncome', 
            'totalExpense', 
            'balance',
            'incomeByCategory',
            'expenseByCategory',
            'monthlyTrend'
        );
        
        // Check if download PDF
        if ($request->has('download') && $request->download === 'pdf') {
            return $this->downloadPdf($data);
        }
        
        return view('finances.report', $data);
    }

    private function downloadPdf($data)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('finances.report-pdf', $data);
        
        $filename = 'laporan-keuangan-' . 
                    \Carbon\Carbon::create()->month($data['month'])->format('F') . 
                    '-' . $data['year'] . '.pdf';
        
        return $pdf->download($filename);
    }

    public function dashboard()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyIncome = Finance::income()->month($currentMonth, $currentYear)->sum('amount');
        $monthlyExpense = Finance::expense()->month($currentMonth, $currentYear)->sum('amount');
        $monthlyBalance = $monthlyIncome - $monthlyExpense;

    
        $ytdIncome = Finance::income()->whereYear('transaction_date', $currentYear)->sum('amount');
        $ytdExpense = Finance::expense()->whereYear('transaction_date', $currentYear)->sum('amount');
        $ytdBalance = $ytdIncome - $ytdExpense;

        $recentTransactions = Finance::orderBy('transaction_date', 'desc')
                                    ->orderBy('created_at', 'desc')
                                    ->take(10)
                                    ->get();

        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $m = $date->month;
            $y = $date->year;
            
            $income = Finance::income()->month($m, $y)->sum('amount');
            $expense = Finance::expense()->month($m, $y)->sum('amount');
            
            $monthlyTrend[] = [
                'month' => $date->format('M Y'),
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense
            ];
        }

        return view('finances.dashboard', compact(
            'monthlyIncome',
            'monthlyExpense',
            'monthlyBalance',
            'ytdIncome',
            'ytdExpense',
            'ytdBalance',
            'recentTransactions',
            'monthlyTrend'
        ));
    }
}