<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['catalog_id', 'name', 'code', 'sku', 'type', 'hex_code', 'color_family', 'price', 'currency', 'stock_quantity', 'stock_status', 'swatch_path', 'is_active'])]
class Color extends Model
{
    /** @use HasFactory<\Database\Factories\ColorFactory> */
    use HasFactory;

    protected $attributes = ['type' => 'plain', 'currency' => 'AED', 'stock_quantity' => 0, 'stock_status' => 'in_stock', 'is_active' => true];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function catalog(): BelongsTo { return $this->belongsTo(Catalog::class); }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'color_user_favorites')->withTimestamps();
    }
}
