<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'type', 'start_date', 'end_date', 'status', 'filters'])]

class ReportRequest extends Model
{
    // Define the casts for the model attributes
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'filters' => 'array',
        ];
    }

    // Define the relationship with the Reservation model
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
