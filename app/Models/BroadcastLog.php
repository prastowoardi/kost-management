<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'broadcast_id',
        'tenant_name',
        'phone',
        'status',
        'error_message',
    ];

    public function broadcast()
    {
        return $this->belongsTo(Broadcast::class);
    }
}