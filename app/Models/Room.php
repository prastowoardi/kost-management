<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'type',
        'price',
        'status',
        'description',
        'capacity',
        'size',
        'images'
    ];

    protected $casts = [
        'images' => 'array'
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function activeTenant()
    {
        return $this->hasOne(Tenant::class)->where('status', 'active');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}