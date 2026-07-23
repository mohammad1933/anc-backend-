<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertNewsletterSubscriptionRequest;
use App\Http\Resources\NewsletterSubscriptionResource;
use App\Models\NewsletterSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsletterSubscriptionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection { return NewsletterSubscriptionResource::collection(NewsletterSubscription::query()->latest()->paginate($request->integer('per_page', 20))); }
    public function store(UpsertNewsletterSubscriptionRequest $request): NewsletterSubscriptionResource
    {
        $subscription = NewsletterSubscription::updateOrCreate(['email' => $request->validated('email')], ['locale' => $request->validated('locale', 'en'), 'status' => 'subscribed', 'subscribed_at' => now(), 'unsubscribed_at' => null]);
        return new NewsletterSubscriptionResource($subscription);
    }
    public function show(NewsletterSubscription $newsletterSubscription): NewsletterSubscriptionResource { return new NewsletterSubscriptionResource($newsletterSubscription); }
    public function update(UpsertNewsletterSubscriptionRequest $request, NewsletterSubscription $newsletterSubscription): NewsletterSubscriptionResource { $newsletterSubscription->update($request->validated()); return new NewsletterSubscriptionResource($newsletterSubscription->refresh()); }
    public function destroy(NewsletterSubscription $newsletterSubscription): JsonResponse { $newsletterSubscription->update(['status' => 'unsubscribed', 'unsubscribed_at' => now()]); return response()->json(status: 204); }
}
