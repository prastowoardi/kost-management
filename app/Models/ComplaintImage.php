<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplaintImage extends Model
{
    use HasFactory, SoftDeletes, \App\Models\Concerns\HasUuidColumn;

    protected $fillable = ['complaint_id', 'path'];

    protected $hidden = ['id'];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    protected $appends = ['full_url'];

    public function getFullUrlAttribute()
    {
        return asset('storage/'.$this->image_path);
    }
}
