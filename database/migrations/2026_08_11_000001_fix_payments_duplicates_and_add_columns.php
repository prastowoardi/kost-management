<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus pembayaran duplikat: pertahankan satu per (tenant, periode),
        // prioritas 'paid', lalu yang tertua. Record finance ikut dihapus.
        $duplicates = DB::table('payments')
            ->whereNull('deleted_at')
            ->select('tenant_id', 'period_month')
            ->groupBy('tenant_id', 'period_month')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            $rows = DB::table('payments')
                ->whereNull('deleted_at')
                ->where('tenant_id', $dup->tenant_id)
                ->where('period_month', $dup->period_month)
                ->orderByRaw("(status = 'paid') DESC, id ASC")
                ->get();

            $keep = $rows->shift();

            foreach ($rows as $row) {
                DB::table('finances')->where('payment_id', $row->id)->delete();
                DB::table('payments')->where('id', $row->id)->delete();
            }
        }

        // Kolom yang sudah ditulis kode namun belum ada di migrasi
        if (! Schema::hasColumn('payments', 'proof_of_payment')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('proof_of_payment')->nullable()->after('receipt_file');
            });
        }

        if (! Schema::hasColumn('payments', 'verified_at')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->timestamp('verified_at')->nullable()->after('status');
            });
        }

        // Cegah pembayaran ganda per (tenant, periode) untuk baris yang masih aktif.
        // Generated column -> NULL saat soft-deleted, jadi soft-delete tidak memblokir input ulang.
        if (! Schema::hasColumn('payments', 'period_active')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->date('period_active')->storedAs('IF(deleted_at IS NULL, period_month, NULL)');
            });

            Schema::table('payments', function (Blueprint $table) {
                $table->unique(['tenant_id', 'period_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'period_active']);
            $table->dropColumn('period_active');
            $table->dropColumn('verified_at');
        });
    }
};
