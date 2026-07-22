<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManualReceipt extends Model
{
    use SoftDeletes, \App\Models\Concerns\HasUuidColumn;
    protected $fillable = [
        'tenant_name',
        'room_number',
        'period',
        'invoice_number',
        'total_amount',
    ];

    protected $hidden = ['id'];
}
