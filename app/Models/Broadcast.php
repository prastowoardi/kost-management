<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Broadcast extends Model
{
    use HasFactory, SoftDeletes, \App\Models\Concerns\HasUuidColumn;

    protected $fillable = [
        'message',
        'total_success',
        'total_failed',
    ];

    protected $hidden = ['id'];

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