<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Check if user is admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Check if user is staff
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    // Check if user is tenant
    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    // Check if user has admin or staff role
    public function isAdminOrStaff(): bool
    {
        return in_array($this->role, ['admin', 'staff']);
    }

    // Check if user is active
    public function isActive(): bool
    {
        return $this->is_active;
    }

    // Relationship with tenant data
    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }
}