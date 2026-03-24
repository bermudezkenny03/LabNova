<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'equipment_id', 'start_time', 'end_time', 'status', 'notes', 'rejection_reason', 'approved_by', 'approved_at'])]
class Reservation extends Model
{
    use SoftDeletes;

    // Define the casts for the model attributes
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship with the Equipment model
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    // Define the relationship with the User model for the approver
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Define the relationship with the ReservationLog model
    public function logs()
    {
        return $this->hasMany(ReservationLog::class);
    }
}
