<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'transaction_date',
        'amount',
        'description',
        'notes',
        'payment_id',
        'receipt_file'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Scope untuk income
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    // Scope untuk expense
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    // Scope untuk bulan tertentu
    public function scopeMonth($query, $month, $year)
    {
        return $query->whereMonth('transaction_date', $month)
                    ->whereYear('transaction_date', $year);
    }
}