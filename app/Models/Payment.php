<?php

namespace App\Models;

use Carbon\Carbon;
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
        'proof_of_payment',
        'verified_at',
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
                // random_bytes + cek keunikan: jauh lebih aman dari tabrakan
                // dibanding uniqid() yang berbasis microsecond.
                do {
                    $candidate = 'INV-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
                } while (static::withTrashed()->where('invoice_number', $candidate)->exists());

                $payment->invoice_number = $candidate;
            }
        });
    }

    public function isDue()
    {
        $dueDate = $this->created_at->addMonth();

        return now()->greaterThanOrEqualTo($dueDate);
    }

    /**
     * Sisa tagihan sebuah periode untuk tenant tertentu.
     * Mendukung pembayaran bertahap (cicilan): sisa = harga - total terbayar.
     */
    public static function remainingForPeriod(int|string $tenantId, string $period, float|int $price): float
    {
        $paid = (float) static::where('tenant_id', $tenantId)
            ->whereDate('period_month', Carbon::parse($period)->startOfMonth()->toDateString())
            ->whereNull('deleted_at')
            ->sum('total');

        return max(0.0, (float) $price - $paid);
    }
}
