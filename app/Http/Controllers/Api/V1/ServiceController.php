<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return ServiceResource::collection(Service::query()->withCount('inquiries')
            ->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')))
            ->orderBy('sort_order')->paginate($request->integer('per_page', 12)));
    }
    public function store(UpsertServiceRequest $request): ServiceResource
    {
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('services', 'public');
        }

        return new ServiceResource(Service::create($data)->loadCount('inquiries'));
    }

    public function show(Service $service): ServiceResource { return new ServiceResource($service->loadCount('inquiries')); }

    public function update(UpsertServiceRequest $request, Service $service): ServiceResource
    {
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $this->deleteImage($service->image_path);
            $data['image_path'] = $request->file('image')->store('services', 'public');
        }
        $service->update($data);

        return new ServiceResource($service->refresh()->loadCount('inquiries'));
    }

    public function destroy(Service $service): JsonResponse
    {
        $this->deleteImage($service->image_path);
        $service->delete();

        return response()->json(status: 204);
    }

    private function deleteImage(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http') && ! str_starts_with($path, '/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
