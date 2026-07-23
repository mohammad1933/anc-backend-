<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $categories = Category::query()->withCount('catalogs')->with('children')
            ->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')))
            ->orderBy('sort_order')->orderBy('name')->paginate($request->integer('per_page', 15));

        return CategoryResource::collection($categories);
    }

    public function store(UpsertCategoryRequest $request): CategoryResource
    {
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        return new CategoryResource(Category::create($data));
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category->load(['parent', 'children'])->loadCount('catalogs'));
    }

    public function update(UpsertCategoryRequest $request, Category $category): CategoryResource
    {
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $this->deleteImage($category->image_path);
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }
        $category->update($data);

        return new CategoryResource($category->refresh());
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->deleteImage($category->image_path);
        $category->delete();

        return response()->json(status: 204);
    }

    private function deleteImage(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http') && ! str_starts_with($path, '/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
