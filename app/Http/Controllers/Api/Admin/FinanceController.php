<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Finance;
use Illuminate\Http\Request;
use Throwable;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Finance::query();

        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $finances = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $summaryQuery = Finance::query();
        if ($request->filled('start_date')) {
            $summaryQuery->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $summaryQuery->whereDate('transaction_date', '<=', $request->end_date);
        }
        if ($request->filled('category') && $request->category !== 'all') {
            $summaryQuery->where('category', $request->category);
        }

        $totalIncome = (float) (clone $summaryQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (float) (clone $summaryQuery)->where('type', 'expense')->sum('amount');

        $categories = Finance::distinct()->pluck('category')->filter()->values();

        return response()->json([
            'data' => $finances,
            'income' => $totalIncome,
            'expense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense,
            'categories' => $categories,
        ]);
    }

    public function show($id)
    {
        $finance = Finance::where(is_numeric($id) ? 'id' : 'uuid', $id)->firstOrFail();

        return response()->json($finance);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'receipt_file' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('receipt_file')) {
            $path = $request->file('receipt_file')->store('receipts');
            $validated['receipt_file'] = $path;
        }

        try {
            $finance = Finance::create($validated);

            LogHelper::log('CREATE_FINANCE', 'Menambah transaksi keuangan via API: '.$finance->category.' Rp'.number_format($finance->amount, 0, ',', '.'), $finance);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil disimpan',
                'data' => $finance,
            ], 201);
        } catch (Throwable $e) {
            LogHelper::logError(
                'CREATE_FINANCE_FAILED',
                'Gagal menyimpan transaksi dari API',
                $e
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data',
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $finance = Finance::where(is_numeric($id) ? 'id' : 'uuid', $id)->firstOrFail();
            $deletedData = $finance->toArray();
            $finance->delete();

            LogHelper::log('DELETE_FINANCE', 'Menghapus transaksi keuangan via API: #'.$finance->id, null, [
                'deleted' => $deletedData,
            ]);

            return response()->json(['message' => 'Transaksi berhasil dihapus']);
        } catch (Throwable $e) {
            LogHelper::logError(
                'DELETE_FINANCE_FAILED',
                "Gagal hapus transaksi #{$id} dari API",
                $e
            );

            return response()->json(['message' => 'Gagal menghapus transaksi'], 500);
        }
    }

    public function getApiCategories()
    {
        $categories = \App\Models\Category::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'incomeCategories' => $categories->where('type', 'income')->pluck('name')->toArray(),
            'expenseCategories' => $categories->where('type', 'expense')->pluck('name')->toArray(),
        ]);
    }
}
