<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Finance;
use Illuminate\Http\Request;
use Throwable;

class FinanceController extends Controller
{
    public function index()
    {
        $finances = Finance::orderBy('created_at', 'asc')->get();

        return response()->json([
            'data' => $finances,
            'income' => $finances->where('type', 'income')->sum('amount'),
            'expense' => $finances->where('type', 'expense')->sum('amount'),
        ]);
    }

    public function show($id)
    {
        $finance = Finance::where('uuid', $id)->firstOrFail();

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
            'receipt_file' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('receipt_file')) {
            $path = $request->file('receipt_file')->store('receipts', 'public');
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
            $finance = Finance::where('uuid', $id)->firstOrFail();
            $deletedData = $finance->toArray();
            $finance->delete();

            LogHelper::log('DELETE_FINANCE', 'Menghapus transaksi keuangan via API: #'.$deletedData['id'], null, [
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
