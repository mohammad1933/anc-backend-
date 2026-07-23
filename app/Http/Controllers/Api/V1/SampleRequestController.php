<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertSampleRequestRequest;
use App\Http\Resources\SampleRequestResource;
use App\Models\SampleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleRequestController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $requests = SampleRequest::query()->with(['customer:id,company_name,contact_name', 'items.catalog:id,name', 'items.color:id,name,code'])
            ->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->string('country')->isNotEmpty(), fn ($query) => $query->where('country', $request->string('country')))
            ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('full_name', 'like', '%'.$request->string('search').'%')->orWhere('company_name', 'like', '%'.$request->string('search').'%')->orWhere('reference', 'like', '%'.$request->string('search').'%');
            }))->latest()->paginate($request->integer('per_page', 10));
        return SampleRequestResource::collection($requests);
    }

    public function store(UpsertSampleRequestRequest $request): SampleRequestResource
    {
        $sampleRequest = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $items = Arr::pull($data, 'items');
            $data['reference'] = 'REQ-'.Str::upper(Str::random(8));
            $sampleRequest = SampleRequest::create($data);
            $sampleRequest->items()->createMany($items);
            return $sampleRequest;
        });
        return new SampleRequestResource($sampleRequest->load('items'));
    }

    public function show(SampleRequest $sampleRequest): SampleRequestResource { return new SampleRequestResource($sampleRequest->load(['customer', 'items.catalog', 'items.color'])); }
    public function update(UpsertSampleRequestRequest $request, SampleRequest $sampleRequest): SampleRequestResource
    {
        DB::transaction(function () use ($request, $sampleRequest) {
            $data = $request->validated();
            $items = Arr::pull($data, 'items');
            $sampleRequest->update($data);
            if ($items !== null) { $sampleRequest->items()->delete(); $sampleRequest->items()->createMany($items); }
        });
        return new SampleRequestResource($sampleRequest->refresh()->load('items'));
    }
    public function destroy(SampleRequest $sampleRequest): JsonResponse { $sampleRequest->delete(); return response()->json(status: 204); }
    public function status(Request $request, SampleRequest $sampleRequest): SampleRequestResource
    {
        $validated = $request->validate(['status' => ['required', 'in:approved,rejected,fulfilled']]);
        $sampleRequest->update(['status' => $validated['status'], 'reviewed_at' => now()]);
        return new SampleRequestResource($sampleRequest->refresh()->load('items'));
    }
}
