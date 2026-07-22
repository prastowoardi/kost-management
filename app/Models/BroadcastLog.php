<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastLog extends Model
{
    use HasFactory, \App\Models\Concerns\HasUuidColumn;

    protected $fillable = [
        'broadcast_id',
        'tenant_name',
        'phone',
        'status',
        'error_message',
    ];

    protected $hidden = ['id'];

    public function broadcast()
    {
        return $this->belongsTo(Broadcast::class);
    }
}