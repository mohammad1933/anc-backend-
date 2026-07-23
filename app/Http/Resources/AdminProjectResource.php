<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminProjectResource extends JsonResource
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
            'status' => $this->status, 'is_favorite' => $this->is_favorite, 'archived_at' => $this->archived_at?->toISOString(),
            'fabrics_count' => count($this->fabrics ?? []), 'colors_count' => count($this->saved_colors ?? []),
            'notes_count' => count($this->notes ?? []), 'inspiration_count' => count($this->inspiration_images ?? []),
            'members_count' => count($this->members ?? []), 'timeline_count' => count($this->timeline ?? []),
            'owner' => $this->whenLoaded('user', fn () => $this->user ? ['id' => $this->user->id, 'name' => $this->user->name, 'email' => $this->user->email] : null),
            'created_at' => $this->created_at?->toISOString(), 'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
