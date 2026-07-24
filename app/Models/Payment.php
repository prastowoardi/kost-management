<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use \App\Models\Concerns\HasUuidColumn, HasFactory, SoftDeletes;

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
        'receipt_file',
    ];

    protected $hidden = ['id'];

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
            if (! $payment->invoice_number) {
                $payment->invoice_number = 'INV-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6));
            }
        });
    }

    public function isDue()
    {
        $dueDate = $this->created_at->addMonth();

        return now()->greaterThanOrEqualTo($dueDate);
    }
}
