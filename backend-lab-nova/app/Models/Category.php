<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'description', 'status'])]
#[Hidden([])]
class Category extends Model
{
    use SoftDeletes;

    // Define the casts for the model attributes
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    // Define the relationship with the Equipment model
    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}
