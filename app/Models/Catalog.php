<?php

namespace App\Models;

use Database\Factories\CatalogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['category_id', 'name', 'slug', 'sku', 'description', 'material', 'composition', 'price', 'currency', 'discount_percent', 'discount_starts_at', 'discount_ends_at', 'applications', 'specifications', 'thumbnail_path', 'pdf_path', 'status', 'is_featured', 'is_new'])]
class Catalog extends Model
{
    /** @use HasFactory<CatalogFactory> */
    use HasFactory;

    protected $attributes = ['status' => 'draft', 'is_featured' => false, 'is_new' => false];

    protected function casts(): array
    {
        return ['applications' => 'array', 'specifications' => 'array', 'price' => 'decimal:2', 'discount_percent' => 'integer', 'discount_starts_at' => 'datetime', 'discount_ends_at' => 'datetime', 'is_featured' => 'boolean', 'is_new' => 'boolean'];
    }

    public function scopeDiscounted(Builder $query): Builder
    {
        return $query->whereNotNull('price')->whereNotNull('discount_percent')
            ->where(fn (Builder $query) => $query->whereNull('discount_starts_at')->orWhere('discount_starts_at', '<=', now()))
            ->where(fn (Builder $query) => $query->whereNull('discount_ends_at')->orWhere('discount_ends_at', '>=', now()));
    }

    public function hasActiveDiscount(): bool
    {
        return $this->price !== null && $this->discount_percent !== null
            && ($this->discount_starts_at === null || $this->discount_starts_at->isPast())
            && ($this->discount_ends_at === null || $this->discount_ends_at->isFuture());
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function colors(): HasMany
    {
        return $this->hasMany(Color::class);
    }
}
