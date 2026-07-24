<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use \App\Models\Concerns\HasUuidColumn, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'is_active',
    ];

    protected $hidden = ['id'];
}
