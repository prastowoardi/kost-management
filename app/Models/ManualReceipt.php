<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualReceipt extends Model
{
    protected $fillable = [
        'tenant_name',
        'room_number',
        'period',
        'invoice_number',
        'total_amount',
    ];
}
