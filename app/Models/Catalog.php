<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['category_id', 'name', 'slug', 'sku', 'description', 'material', 'composition', 'applications', 'specifications', 'thumbnail_path', 'pdf_path', 'status', 'is_featured', 'is_new'])]
class Catalog extends Model
{
    /** @use HasFactory<\Database\Factories\CatalogFactory> */
    use HasFactory;

    protected $attributes = ['status' => 'draft', 'is_featured' => false, 'is_new' => false];

    protected function casts(): array
    {
        return ['applications' => 'array', 'specifications' => 'array', 'is_featured' => 'boolean', 'is_new' => 'boolean'];
    }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function colors(): HasMany { return $this->hasMany(Color::class); }
}
