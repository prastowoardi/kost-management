<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('broadcast_logs', function (Blueprint $table) {
            $table->foreignId('broadcast_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('broadcast_logs', function (Blueprint $table) {
            $table->foreignId('broadcast_id')->nullable(false)->change();
        });
    }
};