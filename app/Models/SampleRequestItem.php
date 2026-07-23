<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['sample_request_id', 'catalog_id', 'color_id', 'sample_name', 'quantity'])]
class SampleRequestItem extends Model
{
    /** @use HasFactory<\Database\Factories\SampleRequestItemFactory> */
    use HasFactory;

    public function sampleRequest(): BelongsTo { return $this->belongsTo(SampleRequest::class); }
    public function catalog(): BelongsTo { return $this->belongsTo(Catalog::class); }
    public function color(): BelongsTo { return $this->belongsTo(Color::class); }
}
