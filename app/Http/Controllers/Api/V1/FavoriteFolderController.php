<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFavoriteFolderRequest;
use App\Http\Resources\FavoriteFolderResource;
use App\Models\FavoriteFolder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FavoriteFolderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return FavoriteFolderResource::collection($request->user()->favoriteFolders()->withCount('favorites')->orderBy('sort_order')->get());
    }

    public function store(StoreFavoriteFolderRequest $request): FavoriteFolderResource
    {
        $nextSortOrder = ((int) $request->user()->favoriteFolders()->max('sort_order')) + 1;
        $folder = $request->user()->favoriteFolders()->create($request->validated() + ['sort_order' => $nextSortOrder]);

        return new FavoriteFolderResource($folder->loadCount('favorites'));
    }

    public function destroy(Request $request, FavoriteFolder $favoriteFolder): JsonResponse
    {
        abort_unless($favoriteFolder->user_id === $request->user()->id, 404);

        $favoriteFolder->delete();

        return response()->json(status: 204);
    }
}
