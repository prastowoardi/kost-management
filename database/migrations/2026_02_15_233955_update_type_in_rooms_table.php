<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('type')->change();
        });

        DB::table('rooms')->update(['type' => 'singlenoac']);

        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('type', ['singlenoac', 'singleac', 'shared'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            //
        });
    }
};
