<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'stock' => $this->stock,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'images' => EquipmentImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at,
        ];
    }
}