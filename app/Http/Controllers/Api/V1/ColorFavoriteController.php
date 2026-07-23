<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Http\Resources\ColorResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ColorFavoriteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return ColorResource::collection(
            $request->user()->favoriteColors()->with('catalog')->get()
        );
    }

    public function toggle(Request $request, Color $color): JsonResponse
    {
        $isFavorite = $request->user()->favoriteColors()->whereKey($color->getKey())->exists();

        if ($isFavorite) {
            $request->user()->favoriteColors()->detach($color);
        } else {
            $request->user()->favoriteColors()->attach($color);
        }

        return response()->json([
            'data' => [
                'color_id' => $color->getKey(),
                'is_favorite' => ! $isFavorite,
            ],
        ]);
    }
}
