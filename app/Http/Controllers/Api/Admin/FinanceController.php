<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use Illuminate\Http\Request;

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
        $request->validate([
            'title'       => 'required|string',
            'amount'      => 'required|numeric',
            'type'        => 'required|in:income,expense',
            'description' => 'nullable|string',
        ]);

        $finance = Finance::create([
            'title'            => $request->title,
            'amount'           => $request->amount,
            'type'             => $request->type,
            'description'      => $request->description,
            'category'         => $request->category ?? 'Umum',
            'transaction_date' => $request->date ?? now()->toDateString(),
        ]);

        return response()->json($finance, 201);
    }

    public function destroy($id)
    {
        Finance::findOrFail($id)->delete();
        return response()->json(['message' => 'Transaksi berhasil dihapus']);
    }
}