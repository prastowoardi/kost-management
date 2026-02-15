<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'room_id',
        'invoice_number',
        'payment_date',
        'period_month',
        'amount',
        'late_fee',
        'total',
        'status',
        'payment_method',
        'notes',
        'receipt_file'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'period_month' => 'date',
    ];

    public function finance()
    {
        return $this->hasOne(Finance::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->invoice_number) {
                $count = Payment::whereDate('created_at', today())->count() + 1;
                $payment->invoice_number = 'INV-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function isDue()
    {
        $dueDate = $this->created_at->addMonth(); 
        
        return now()->greaterThanOrEqualTo($dueDate);
    }
}