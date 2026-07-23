<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['customer_id', 'reference', 'company_name', 'industry', 'full_name', 'country', 'delivery_address', 'city', 'email', 'phone', 'notes', 'status', 'reviewed_at'])]
class SampleRequest extends Model
{
    /** @use HasFactory<\Database\Factories\SampleRequestFactory> */
    use HasFactory;

    protected $attributes = ['status' => 'pending'];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function items(): HasMany { return $this->hasMany(SampleRequestItem::class); }
}
