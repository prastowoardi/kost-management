<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ManualReceipt extends Model
{
    protected $fillable = [
        'tenant_name',
        'room_number',
        'period',
        'invoice_number',
        'total_amount'
    ];

    public function up()
    {
        Schema::create('manual_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_name');
            $table->string('room_number');
            $table->string('period');
            $table->string('invoice_number')->unique();
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
        });
    }
}
