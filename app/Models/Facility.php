<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory, \App\Models\Concerns\HasUuidColumn;

    protected $fillable = [
        'name',
        'description',
        'type',
        'quantity',
        'condition'
    ];

    protected $hidden = ['id'];

    public function rooms()
    {
        return $this->belongsToMany(Room::class);
    }
}