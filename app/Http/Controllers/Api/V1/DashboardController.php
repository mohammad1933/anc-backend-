<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Color;
use App\Models\Customer;
use App\Models\Inquiry;
use App\Models\SampleRequest;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'catalogs' => ['total' => Catalog::count(), 'published' => Catalog::where('status', 'published')->count(), 'missing_specs' => Catalog::whereNull('specifications')->count()],
            'sample_requests' => ['total' => SampleRequest::count(), 'pending' => SampleRequest::where('status', 'pending')->count(), 'recent' => SampleRequest::with('items')->latest()->limit(5)->get()],
            'customers' => ['total' => Customer::count(), 'active' => Customer::where('status', 'active')->count()],
            'inquiries' => ['total' => Inquiry::count(), 'new' => Inquiry::where('status', 'new')->count(), 'recent' => Inquiry::latest()->limit(5)->get()],
            'trending_colors' => Color::query()->with('catalog:id,name')->orderByDesc('view_count')->limit(5)->get(),
            'top_catalogs' => Catalog::query()->withCount('colors')->orderByDesc('view_count')->limit(5)->get(),
        ]);
    }
}
