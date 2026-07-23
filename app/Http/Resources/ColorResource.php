<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ColorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['swatch_url'] = $this->imageUrl($this->swatch_path);
        $data['swatch_path'] = $data['swatch_url'];

        return $data;
    }

    private function imageUrl(?string $path): ?string
    {
        return $path && ! str_starts_with($path, 'http') && ! str_starts_with($path, '/')
            ? Storage::disk('public')->url($path)
            : $path;
    }
}
