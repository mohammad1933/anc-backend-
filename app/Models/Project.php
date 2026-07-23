<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'name', 'client', 'description', 'cover_image', 'status', 'is_favorite', 'fabrics', 'saved_colors', 'palette', 'notes', 'inspiration_images', 'members', 'timeline', 'recent_activity', 'archived_at'])]
class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $attributes = ['status' => 'active', 'is_favorite' => false];

    protected function casts(): array
    {
        return [
            'is_favorite' => 'boolean', 'fabrics' => 'array', 'saved_colors' => 'array', 'palette' => 'array',
            'notes' => 'array', 'inspiration_images' => 'array', 'members' => 'array',
            'timeline' => 'array', 'recent_activity' => 'array', 'archived_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }
}
