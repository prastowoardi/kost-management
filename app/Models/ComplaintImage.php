<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ComplaintImage extends Model
{
    use \App\Models\Concerns\HasUuidColumn, HasFactory, SoftDeletes;

    protected $fillable = ['complaint_id', 'image_path', 'path'];

    protected $hidden = ['id'];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    protected $appends = ['full_url'];

    public function getPathAttribute($value)
    {
        return $value ?: ($this->attributes['image_path'] ?? null);
    }

    public function getFullUrlAttribute()
    {
        $path = $this->path ?: ($this->attributes['image_path'] ?? null);

        return $path ? Storage::url($path) : null;
    }
}
