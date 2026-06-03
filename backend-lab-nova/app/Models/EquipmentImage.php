<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

#[Fillable(['image_path', 'image_name', 'is_primary', 'equipment_id'])]
#[Appends(['image_url'])]
class EquipmentImage extends Model
{
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ?: null;
    }

    public static function saveImages($uploadedImages, int $equipmentId): void
    {
        $files = is_array($uploadedImages) ? $uploadedImages : [$uploadedImages];

        foreach ($files as $index => $uploadedFile) {
            $result = Cloudinary::upload($uploadedFile->getRealPath(), [
                'folder'    => "equipment_images/{$equipmentId}",
                'public_id' => pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
            ]);

            $url = $result->getSecurePath();

            self::create([
                'image_path'   => $url,
                'image_name'   => $uploadedFile->getClientOriginalName(),
                'is_primary'   => $index === 0,
                'equipment_id' => $equipmentId,
            ]);
        }
    }
}
