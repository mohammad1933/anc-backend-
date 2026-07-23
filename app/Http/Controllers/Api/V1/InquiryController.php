<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertInquiryRequest;
use App\Http\Resources\InquiryResource;
use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InquiryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return InquiryResource::collection(Inquiry::query()->with(['service:id,title', 'catalog:id,name', 'color:id,name,code'])
            ->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->string('department')->isNotEmpty(), fn ($query) => $query->where('department', $request->string('department')))
            ->latest()->paginate($request->integer('per_page', 10)));
    }
    public function store(UpsertInquiryRequest $request): InquiryResource { return new InquiryResource(Inquiry::create($request->validated())); }
    public function show(Inquiry $inquiry): InquiryResource { return new InquiryResource($inquiry->load(['customer', 'service', 'catalog', 'color'])); }
    public function update(UpsertInquiryRequest $request, Inquiry $inquiry): InquiryResource { $inquiry->update($request->validated()); return new InquiryResource($inquiry->refresh()); }
    public function destroy(Inquiry $inquiry): JsonResponse { $inquiry->delete(); return response()->json(status: 204); }
}
