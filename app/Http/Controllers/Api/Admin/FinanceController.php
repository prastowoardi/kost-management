<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $finance = Finance::findOrFail($id);
        return response()->json($finance);
    }

    public function store(Request $request)
    {
        Log::info('Jenis receipt_file: ' . gettype($request->file('receipt_file')));
        Log::info('Data Request:', $request->all());

        $validated = $request->validate([
            'type'             => 'required|in:income,expense',
            'category'         => 'required|string|max:255',
            'amount'           => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description'      => 'nullable|string',
            'receipt_file'     => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('receipt_file')) {
            $path = $request->file('receipt_file')->store('receipts', 'public');
            $validated['receipt_file'] = $path;
        }

        try {
            $finance = \App\Models\Finance::create($validated);
            
            return response()->json([
                'status'  => 'success',
                'message' => 'Transaksi berhasil disimpan',
                'data'    => $finance
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        Finance::findOrFail($id)->delete();
        return response()->json(['message' => 'Transaksi berhasil dihapus']);
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