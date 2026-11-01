<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Finance extends Model
{
    use \App\Models\Concerns\HasUuidColumn, \App\Models\Concerns\InvalidatesDashboardAnalytics, HasFactory, SoftDeletes;

    protected $fillable = [
        'type', 'category', 'transaction_date', 'amount', 'description', 'notes', 'receipt_file', 'payment_id',
    ];

    protected $hidden = ['id'];

    protected $appends = ['receipt_url', 'receipt_path'];

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

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    /**
     * Resolusi path file bukti transaksi dari receipt_file atau dari relasi payment.
     */
    public function getReceiptPathAttribute(): ?string
    {
        if ($this->receipt_file) {
            return $this->receipt_file;
        }

        return $this->payment?->receipt_path;
    }

    /**
     * URL file bukti transaksi untuk tampilan web dan API.
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
}
