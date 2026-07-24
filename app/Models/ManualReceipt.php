<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManualReceipt extends Model
{
    use \App\Models\Concerns\HasUuidColumn, SoftDeletes;

    protected $fillable = [
        'tenant_name',
        'room_number',
        'period',
        'invoice_number',
        'total_amount',
    ];

    protected $hidden = ['id'];
}
