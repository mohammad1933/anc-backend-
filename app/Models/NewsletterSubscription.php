<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['email', 'locale', 'status', 'subscribed_at', 'unsubscribed_at'])]
class NewsletterSubscription extends Model
{
    /** @use HasFactory<\Database\Factories\NewsletterSubscriptionFactory> */
    use HasFactory;

    protected $attributes = ['locale' => 'en', 'status' => 'subscribed'];

    protected function casts(): array
    {
        return ['subscribed_at' => 'datetime', 'unsubscribed_at' => 'datetime'];
    }
}
