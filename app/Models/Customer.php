<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['company_name', 'contact_name', 'email', 'phone', 'industry', 'country', 'city', 'address', 'tier', 'status'])]
class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    protected $attributes = ['tier' => 'standard', 'status' => 'active'];

    public function sampleRequests(): HasMany { return $this->hasMany(SampleRequest::class); }
    public function inquiries(): HasMany { return $this->hasMany(Inquiry::class); }
}
