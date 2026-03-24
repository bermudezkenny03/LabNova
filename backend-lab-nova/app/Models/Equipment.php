<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['category_id', 'name', 'code', 'description', 'stock', 'status', 'is_active'])]
#[Hidden([])]
class Equipment extends Model
{

    // Define the casts for the model attributes
    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'status' => 'string',
            'is_active' => 'boolean',
        ];
    }

    // Define the relationship with the Category model
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Define the relationship with the EquipmentImage model
    public function images()
    {
        return $this->hasMany(EquipmentImage::class);
    }

    // Define the relationship with the Reservation model
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
