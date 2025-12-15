<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // Penting untuk type hinting Builder

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        // ... (fillable Anda)
        'type', 'category', 'transaction_date', 'amount', 'description', 'notes', 'receipt_file', 'payment_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'integer',
    ];

    // --- LOCAL SCOPES (Solusi untuk Error income() dan month()) ---

    /**
     * Scope untuk mengambil transaksi bertipe 'income' (pemasukan).
     */
    public function scopeIncome(Builder $query): void
    {
        $query->where('type', 'income');
    }

    /**
     * Scope untuk mengambil transaksi bertipe 'expense' (pengeluaran).
     */
    public function scopeExpense(Builder $query): void
    {
        $query->where('type', 'expense');
    }

    /**
     * Scope untuk memfilter berdasarkan bulan dan tahun.
     * Menggantikan panggilan method month() yang error.
     * Usage: Finance::byMonthYear(12, 2025)->get()
     */
    public function scopeByMonthYear(Builder $query, int $month, int $year): void
    {
        $query->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month);
    }
}