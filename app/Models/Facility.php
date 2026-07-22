<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use HasFactory, SoftDeletes, \App\Models\Concerns\HasUuidColumn;

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