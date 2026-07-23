<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertCatalogRequest;
use App\Http\Resources\CatalogResource;
use App\Models\Catalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $catalogs = Catalog::query()->with(['category:id,name,slug', 'colors:id,catalog_id,color_family'])->withCount('colors')
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->boolean('featured'), fn ($query) => $query->where('is_featured', true))
            ->when($request->string('material')->isNotEmpty(), fn ($query) => $query->where('material', $request->string('material')))
            ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->string('search').'%')->orWhere('sku', 'like', '%'.$request->string('search').'%');
            }))->latest()->paginate($request->integer('per_page', 12));

        return CatalogResource::collection($catalogs);
    }

    public function store(UpsertCatalogRequest $request): CatalogResource
    {
        $data = $request->safe()->except(['thumbnail', 'pdf']);
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('catalogs', 'public');
        }
        if ($request->hasFile('pdf')) {
            $data['pdf_path'] = $request->file('pdf')->store('catalog-pdfs', 'public');
        }

        return new CatalogResource(Catalog::create($data)->load('category'));
    }

    public function show(Catalog $catalog): CatalogResource { return new CatalogResource($catalog->load(['category', 'colors'])->loadCount('colors')); }

    public function update(UpsertCatalogRequest $request, Catalog $catalog): CatalogResource
    {
        $data = $request->safe()->except(['thumbnail', 'pdf']);
        if ($request->hasFile('thumbnail')) {
            $this->deleteImage($catalog->thumbnail_path);
            $data['thumbnail_path'] = $request->file('thumbnail')->store('catalogs', 'public');
        }
        if ($request->hasFile('pdf')) {
            $this->deleteFile($catalog->pdf_path);
            $data['pdf_path'] = $request->file('pdf')->store('catalog-pdfs', 'public');
        }
        $catalog->update($data);

        return new CatalogResource($catalog->refresh()->load('category'));
    }

    public function destroy(Catalog $catalog): JsonResponse
    {
        $this->deleteImage($catalog->thumbnail_path);
        $this->deleteFile($catalog->pdf_path);
        $catalog->delete();

        return response()->json(status: 204);
    }

    private function deleteImage(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http') && ! str_starts_with($path, '/')) {
            Storage::disk('public')->delete($path);
        }
    }

    private function deleteFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http') && ! str_starts_with($path, '/')) {
            Storage::disk('public')->delete($path);
        }
    }

}
