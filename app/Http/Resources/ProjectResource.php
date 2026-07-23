<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'name' => $this->name, 'client' => $this->client, 'description' => $this->description,
            'cover_image' => $this->imageUrl($this->cover_image), 'status' => $this->status,
            'is_favorite' => $this->is_favorite, 'fabrics' => $this->fabrics ?? [],
            'fabrics_count' => count($this->fabrics ?? []), 'saved_colors' => collect($this->saved_colors ?? [])->map(fn ($color) => is_string($color) ? ['name' => strtoupper($color), 'hex' => $color] : $color)->values()->all(), 'palette' => $this->palette ?? [],
            'notes' => $this->notes ?? [], 'inspiration_images' => collect($this->inspiration_images ?? [])->map(fn ($path) => $this->imageUrl($path))->all(),
            'members' => $this->members ?? [], 'timeline' => $this->timeline ?? [],
            'recent_activity' => $this->recent_activity ?? [], 'archived_at' => $this->archived_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(), 'updated_human' => $this->updated_at?->diffForHumans(),
        ];
    }

    private function imageUrl(?string $path): ?string
    {
        return $path && ! str_starts_with($path, 'http') && ! str_starts_with($path, '/')
            ? Storage::disk('public')->url($path)
            : $path;
    }
}
