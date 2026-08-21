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
                $payment->invoice_number = 'INV-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6));
            }
        });
    }

    public function isDue()
    {
        $dueDate = $this->created_at->addMonth();

        return now()->greaterThanOrEqualTo($dueDate);
    }

    public static function paidAmountForPeriod(int $tenantId, string $periodMonth): float
    {
        [$start, $end] = static::periodRange($periodMonth);

        return (float) static::where('tenant_id', $tenantId)
            ->whereBetween('period_month', [$start, $end])
            ->where('status', 'paid')
            ->whereNull('deleted_at')
            ->sum('total');
    }

    public static function remainingForPeriod(int $tenantId, string $periodMonth, float $price): float
    {
        return max(0, $price - static::paidAmountForPeriod($tenantId, $periodMonth));
    }

    public static function normalizePeriodMonth(string $periodMonth): string
    {
        return \Carbon\Carbon::parse($periodMonth)->format('Y-m-01');
    }

    public static function periodRange(string $periodMonth): array
    {
        $month = \Carbon\Carbon::parse($periodMonth);

        return [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()];
    }

    public static function splitPaymentNote(int $sequence, float $remainingAfter): string
    {
        $prefix = 'Cicilan ke-'.$sequence.' dari pembayaran bertahap';

        return $remainingAfter > 0
            ? $prefix.' — sisa Rp '.number_format($remainingAfter, 0, ',', '.').'.'
            : $prefix.' — lunas.';
    }
}
