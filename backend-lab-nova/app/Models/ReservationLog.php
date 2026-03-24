<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['reservation_id', 'user_id', 'action', 'description'])]
class ReservationLog extends Model
{
    use SoftDeletes;

    // Define the relationship with the Reservation model
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
