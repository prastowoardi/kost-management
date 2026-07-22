<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, \App\Models\Concerns\HasUuidColumn;

    protected $fillable = [
        'name',
        'type',
        'is_active',
    ];

    protected $hidden = ['id'];
}