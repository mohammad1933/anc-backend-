<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'favorite_folder_id' => $this->favorite_folder_id,
            'type' => $this->type,
            'name' => $this->name,
            'collection' => $this->collection,
            'material' => $this->material,
            'image_url' => $this->image_url,
            'colors' => $this->colors ?? [],
            'sample_requested_at' => $this->sample_requested_at?->toISOString(),
            'folder' => new FavoriteFolderResource($this->whenLoaded('folder')),
        ];
    }
}
