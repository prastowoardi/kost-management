<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BroadcastLog extends Model
{
    use \App\Models\Concerns\HasUuidColumn, HasFactory, SoftDeletes;

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
