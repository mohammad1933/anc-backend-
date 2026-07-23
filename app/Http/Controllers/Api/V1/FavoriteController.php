<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Requests\UpdateFavoriteRequest;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FavoriteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return FavoriteResource::collection($request->user()->favorites()->with('folder:id,name')
            ->when($request->integer('folder'), fn ($query, int $folderId) => $query->where('favorite_folder_id', $folderId))
            ->oldest()->get());
    }

    public function store(StoreFavoriteRequest $request): FavoriteResource
    {
        return new FavoriteResource($request->user()->favorites()->create($request->validated())->load('folder:id,name'));
    }

    public function show(Request $request, Favorite $favorite): FavoriteResource
    {
        abort_unless($favorite->user_id === $request->user()->id, 404);

        return new FavoriteResource($favorite->load('folder:id,name'));
    }

    public function update(UpdateFavoriteRequest $request, Favorite $favorite): FavoriteResource
    {
        abort_unless($favorite->user_id === $request->user()->id, 404);

        $favorite->update($request->validated());

        return new FavoriteResource($favorite->refresh()->load('folder:id,name'));
    }

    public function destroy(Request $request, Favorite $favorite): JsonResponse
    {
        abort_unless($favorite->user_id === $request->user()->id, 404);

        $favorite->delete();

        return response()->json(status: 204);
    }
}
