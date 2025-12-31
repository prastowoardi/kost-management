<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Category;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $defaultEndDate = now()->format('Y-m-d');
        $defaultStartDate = now()->subDays(30)->format('Y-m-d');

        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);

        $query = Finance::query();

        if ($request->filled('type')) {
            // Menggunakan scope income/expense yang baru
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

        // Menggunakan scope income() dan expense() yang baru
        $totalIncome = Finance::income()
            ->where('transaction_date', '>=', $startDate)
            ->where('transaction_date', '<=', $endDate)
            ->sum('amount');

        $totalExpense = Finance::expense()
            ->where('transaction_date', '>=', $startDate)
            ->where('transaction_date', '<=', $endDate)
            ->sum('amount');
        
        // ... (compact dan return view)
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

    public function report(Request $request)
    {
        // PERBAIKAN: Cast bulan/tahun ke integer agar aman saat dikirim ke Carbon
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        // ... (Logika Report - menggunakan whereBetween, yang sudah benar)
        
        $finances = Finance::whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();
        
        $totalIncome = Finance::income() // Menggunakan scope income()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
        
        $totalExpense = Finance::expense() // Menggunakan scope expense()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
        
        $balance = $totalIncome - $totalExpense;
        
        $incomeByCategory = Finance::income()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        $expenseByCategory = Finance::expense()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        // Monthly Trend (6 bulan)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $m = $date->month;
            $y = $date->year;
            
            $income = Finance::income()->byMonthYear($m, $y)->sum('amount');
            $expense = Finance::expense()->byMonthYear($m, $y)->sum('amount');
            
            $monthlyTrend[] = [
                'month' => $date->format('F Y'),
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense, 
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
                    // PERBAIKAN: Gunakan createFromDate yang aman
                    \Carbon\Carbon::createFromDate($data['year'], $data['month'], 1)->format('F') . 
                    '-' . $data['year'] . '.pdf';
        
        return $pdf->download($filename);
    }

    public function dashboard()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // ... (Logika monthlyIncome, monthlyExpense, ytdStats tetap sama)

        $monthlyIncome = Finance::income()->byMonthYear($currentMonth, $currentYear)->sum('amount');
        $monthlyExpense = Finance::expense()->byMonthYear($currentMonth, $currentYear)->sum('amount');
        $monthlyBalance = $monthlyIncome - $monthlyExpense;

        $ytdIncome = Finance::income()->whereYear('transaction_date', $currentYear)->sum('amount');
        $ytdExpense = Finance::expense()->whereYear('transaction_date', $currentYear)->sum('amount');
        $ytdBalance = $ytdIncome - $ytdExpense;

        $recentTransactions = Finance::orderBy('transaction_date', 'desc')
                                    ->orderBy('created_at', 'desc')
                                    ->take(10)
                                    ->get();

        // --- PERBAIKAN LOGIKA MONTHLY TREND (Semua Bulan Tahun Ini) ---
        
        // 1. Ambil data agregat (income, expense) dari database untuk tahun ini
        $monthlyDataRaw = Finance::selectRaw("
            DATE_FORMAT(transaction_date, '%m') as month_num,
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
        ")
        ->whereYear('transaction_date', $currentYear)
        ->groupBy('month_num')
        ->orderBy('month_num')
        ->get()
        ->keyBy('month_num');

        // 2. Isi array tren untuk 12 bulan (hanya sampai bulan saat ini)
        $monthlyTrend = [];
        $today = now();

        for ($m = 1; $m <= $today->month; $m++) {
            $monthKey = str_pad($m, 2, '0', STR_PAD_LEFT);
            $data = $monthlyDataRaw->get($monthKey);
            
            $income = $data->income ?? 0;
            $expense = $data->expense ?? 0;
            
            $monthlyTrend[] = [
                // Menggunakan Carbon untuk nama bulan, dijamin tidak error
                'month' => \Carbon\Carbon::createFromDate($currentYear, $m, 1)->format('M Y'),
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense
            ];
        }
        
        $monthlyTrend = array_reverse($monthlyTrend); 

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

    public function create()
    {
        return view('finances.create', $this->getCategories());
    }

    public function store(Request $request)
    {
        $cleanAmount = preg_replace('/[^0-9]/', '', $request->amount);
        $request->merge(['amount' => $cleanAmount]);
        
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Finance::create($validated);

        return redirect()->route('finances.index')
            ->with('success', 'Data keuangan berhasil ditambahkan!');
    }

    public function show(Finance $finance)
    {
        return view('finances.show', compact('finance'));
    }

    public function edit(Finance $finance)
    {
        return view('finances.edit', array_merge(compact('finance'), $this->getCategories()));
    }

    public function update(Request $request, Finance $finance)
    {
        $cleanAmount = preg_replace('/[^0-9]/', '', $request->amount);
        $request->merge(['amount' => $cleanAmount]);

        if ($request->has('amount')) {
            $request->merge([
                'amount' => str_replace('.', '', $request->amount)
            ]);
        }

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('receipt_file')) {
            if ($finance->receipt_file) {
                Storage::disk('public')->delete($finance->receipt_file);
            }
            $validated['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
        }

        $finance->update($validated);
        $finance->touch(); 

        return redirect()->route('finances.index')
            ->with('success', 'Data keuangan berhasil diupdate!');
    }

    public function destroy(Finance $finance)
    {
        $finance->delete();

        return redirect()->route('finances.index')
            ->with('success', 'Data keuangan berhasil dihapus!');
    }

    // private function getCategories()
    // {
    //     $incomeCategories = [
    //         'Pembayaran Sewa',
    //         'Deposit',
    //         'Denda Keterlambatan',
    //         'Biaya Listrik',
    //         'Biaya Air',
    //         'Lainnya'
    //     ];

    //     $expenseCategories = [
    //         'Gaji Karyawan',
    //         'Listrik',
    //         'Air',
    //         'Internet',
    //         'Perawatan Bangunan',
    //         'Perbaikan Fasilitas',
    //         'Kebersihan',
    //         'Keamanan',
    //         'Pajak',
    //         'Lainnya'
    //     ];

    //     return compact('incomeCategories', 'expenseCategories');
    // }
    private function getCategories()
    {
        $categories = Category::where('is_active', true)
                                ->orderBy('name', 'asc')
                                ->get();

        $incomeCategories = $categories
            ->where('type', 'income')
            ->pluck('name')
            ->toArray();

        $expenseCategories = $categories
            ->where('type', 'expense')
            ->pluck('name')
            ->toArray();

        // Catatan: Jika ingin menyimpan array sebagai Collection, 
        // bisa menghilangkan ->toArray()

        return compact('incomeCategories', 'expenseCategories');
    }
}