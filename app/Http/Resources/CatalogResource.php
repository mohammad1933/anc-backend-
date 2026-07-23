<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CatalogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['thumbnail_path'] = $this->imageUrl($this->thumbnail_path);
        $data['pdf_path'] = $this->imageUrl($this->pdf_path);

        return $data;
    }

    private function imageUrl(?string $path): ?string
    {
        return $path && ! str_starts_with($path, 'http') && ! str_starts_with($path, '/')
            ? Storage::disk('public')->url($path)
            : $path;
    }
}
