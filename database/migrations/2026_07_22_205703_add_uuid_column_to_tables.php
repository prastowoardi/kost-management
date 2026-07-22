<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private array $tables = [
        'categories', 'facilities', 'rooms', 'users', 'broadcasts', 'manual_receipts',
        'tenants', 'payments', 'complaints', 'facility_room', 'complaint_images',
        'broadcast_logs', 'finances', 'activity_logs', 'sessions', 'personal_access_tokens',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) continue;

            Schema::table($table, function (Blueprint $table) {
                $table->uuid('uuid')->after('id')->nullable();
            });

            DB::table($table)->orderBy('id')->chunk(200, function ($records) use ($table) {
                foreach ($records as $record) {
                    DB::table($table)
                        ->where('id', $record->id)
                        ->update(['uuid' => (string) Str::uuid()]);
                }
            });

            Schema::table($table, function (Blueprint $table) {
                $table->unique('uuid');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'uuid')) continue;

            Schema::table($table, function (Blueprint $table) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            });
        }
    }
};
