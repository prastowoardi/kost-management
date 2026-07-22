<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualReceipt extends Model
{
    use \App\Models\Concerns\HasUuidColumn;
    protected $fillable = [
        'tenant_name',
        'room_number',
        'period',
        'invoice_number',
        'total_amount',
    ];

    protected $hidden = ['id'];
}
