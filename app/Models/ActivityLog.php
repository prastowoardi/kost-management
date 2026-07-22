<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use \App\Models\Concerns\HasUuidColumn;
    protected $fillable = ['user_id', 'action', 'model_type', 'model_id', 'description', 'payload', 'ip_address', 'user_agent'];

    protected $hidden = ['id'];

    protected $casts = ['payload' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
