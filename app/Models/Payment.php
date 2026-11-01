<?php

namespace App\Models;

use App\Http\Controllers\DashboardController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Payment extends Model
{
    use \App\Models\Concerns\HasUuidColumn, \App\Models\Concerns\InvalidatesDashboardAnalytics, HasFactory, SoftDeletes;

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

    protected $appends = ['receipt_path', 'receipt_url'];

    protected $casts = [
        'payment_date' => 'date:Y-m-d',
        'period_month' => 'date:Y-m-d',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Prepare a date for array / JSON serialization in application timezone.
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

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

        foreach (['saved', 'deleted', 'restored', 'forceDeleted'] as $event) {
            static::$event(function () {
                Cache::forget(DashboardController::DUE_TENANTS_CACHE_KEY);
            });
        }
    }

    public function isDue()
    {
        $dueDate = $this->created_at->addMonth();

        return now()->greaterThanOrEqualTo($dueDate);
    }

    /**
     * Resolusi path file bukti bayar dari receipt_file atau proof_of_payment.
     */
    public function getReceiptPathAttribute(): ?string
    {
        $path = $this->receipt_file ?: $this->proof_of_payment;
        if (! $path) {
            return null;
        }

        if (! Str::contains($path, '/')) {
            return 'proofs/'.$path;
        }

        return $path;
    }

    /**
     * URL file bukti bayar untuk tampilan web dan mobile API.
     * Cerdas mendeteksi lokasi file di disk lokal (public) maupun cloud (AWS / Cloudflare R2).
     */
    public function getReceiptUrlAttribute(): ?string
    {
        $path = $this->receipt_path;
        if (! $path) {
            return null;
        }

        $defaultDisk = config('filesystems.default', 'public');

        // Jika default disk diatur ke s3 / Cloudflare R2:
        if ($defaultDisk === 's3') {
            return Storage::disk('s3')->url($path);
        }

        // Cek disk public lokal terlebih dahulu:
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        // Jika tidak ada di lokal public tapi S3 dikonfigurasi, arahkan ke URL S3/R2
        if (filled(config('filesystems.disks.s3.bucket')) && filled(config('filesystems.disks.s3.key'))) {
            return Storage::disk('s3')->url($path);
        }

        return Storage::url($path);
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
