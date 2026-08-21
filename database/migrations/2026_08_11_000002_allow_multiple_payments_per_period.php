<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Izinkan pembayaran bertahap: satu periode boleh punya beberapa catatan
        // pembayaran. Status "lunas" dihitung dari total pembayaran per periode.
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropUnique(['tenant_id', 'period_active']);
            $table->dropColumn('period_active');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->date('period_active')->storedAs('IF(deleted_at IS NULL, period_month, NULL)');
            $table->unique(['tenant_id', 'period_active']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }
};
