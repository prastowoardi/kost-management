<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('broadcast_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broadcast_id')->constrained()->onDelete('cascade');
            $table->string('tenant_name');
            $table->string('phone');
            $table->enum('status', ['success', 'failed']);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcast_logs');
    }
};
