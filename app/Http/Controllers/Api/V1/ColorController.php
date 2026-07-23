<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertColorRequest;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class ColorController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $colors = Color::query()->with('catalog:id,name,slug')
            ->when($request->filled('catalog_id'), fn ($query) => $query->where('catalog_id', $request->integer('catalog_id')))
            ->when($request->string('type')->isNotEmpty(), fn ($query) => $query->where('type', $request->string('type')))
            ->when($request->string('stock_status')->isNotEmpty(), fn ($query) => $query->where('stock_status', $request->string('stock_status')))
            ->when($request->string('color_family')->isNotEmpty(), fn ($query) => $query->where('color_family', $request->string('color_family')))
            ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->string('search').'%')->orWhere('sku', 'like', '%'.$request->string('search').'%');
            }))->latest()->paginate($request->integer('per_page', 24));

        return ColorResource::collection($colors);
    }

    public function store(UpsertColorRequest $request): ColorResource
    {
        $data = $request->safe()->except('swatch');
        if ($request->hasFile('swatch')) {
            $data['swatch_path'] = $request->file('swatch')->store('colors', 'public');
        }

        return new ColorResource(Color::create($data)->load('catalog'));
    }

    public function show(Color $color): ColorResource { return new ColorResource($color->load('catalog')); }

    public function update(UpsertColorRequest $request, Color $color): ColorResource
    {
        $data = $request->safe()->except('swatch');
        if ($request->hasFile('swatch')) {
            $this->deleteImage($color->swatch_path);
            $data['swatch_path'] = $request->file('swatch')->store('colors', 'public');
        }
        $color->update($data);

        return new ColorResource($color->refresh()->load('catalog'));
    }

    public function destroy(Color $color): JsonResponse
    {
        $this->deleteImage($color->swatch_path);
        $color->delete();

        return response()->json(status: 204);
    }

    public function toggle(Color $color): ColorResource { $color->update(['is_active' => ! $color->is_active]); return new ColorResource($color->refresh()); }

    private function deleteImage(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http') && ! str_starts_with($path, '/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
