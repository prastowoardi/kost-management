<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use \App\Models\Concerns\HasUuidColumn, HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'user_id',
        'name',
        'email',
        'phone',
        'id_card',
        'address',
        'entry_date',
        'exit_date',
        'status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'photo',
    ];

    protected $hidden = ['id'];

    protected $casts = [
        'entry_date' => 'date',
        'exit_date' => 'date',
    ];

    protected $appends = ['calculated_due_date', 'days_left'];

    public function getCalculatedDueDateAttribute()
    {
        if (! $this->entry_date) {
            return null;
        }

        $now = Carbon::now()->startOfDay();
        $entryDate = Carbon::parse($this->entry_date)->startOfDay();

        if ($entryDate->greaterThan($now)) {
            return null;
        }

        $targetDate = Carbon::now()->setDay($entryDate->day)->startOfDay();

        $diff = $now->diffInDays($targetDate, false);
        if ($diff < -20) {
            $targetDate->addMonth();
        } elseif ($diff > 20) {
            $targetDate->subMonth();
        }

        return $targetDate;
    }

    public function getDaysLeftAttribute()
    {
        $dueDate = $this->calculated_due_date;

        if (! $dueDate) {
            return null;
        }

        return (int) Carbon::now()->startOfDay()->diffInDays($dueDate, false);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
