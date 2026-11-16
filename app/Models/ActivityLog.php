<?php

namespace App\Models;

use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use \App\Models\Concerns\HasUuidColumn;
    use MassPrunable;

    protected $fillable = ['user_id', 'action', 'model_type', 'model_id', 'description', 'payload', 'ip_address', 'user_agent'];

    protected $hidden = ['id'];

    protected $casts = ['payload' => 'array'];

    /**
     * Get the prunable model query (log lebih dari 60 hari otomatis dibersihkan).
     */
    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDays(60));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
