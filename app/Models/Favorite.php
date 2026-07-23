<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'favorite_folder_id', 'catalog_id', 'type', 'name', 'collection', 'material', 'image_url', 'colors', 'sample_requested_at'])]
class Favorite extends Model
{
    /** @use HasFactory<\Database\Factories\FavoriteFactory> */
    use HasFactory;

    protected $attributes = ['type' => 'collection'];

    protected function casts(): array
    {
        return ['colors' => 'array', 'sample_requested_at' => 'datetime'];
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(FavoriteFolder::class, 'favorite_folder_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }
}
