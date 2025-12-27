<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'total_success',
        'total_failed',
    ];

    public function logs()
    {
        return $this->hasMany(BroadcastLog::class);
    }

    protected static function booted()
    {
        static::deleting(function ($broadcast) {
            $broadcast->logs()->delete();
        });
    }
}