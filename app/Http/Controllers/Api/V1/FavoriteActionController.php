<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FavoriteActionRequest;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use App\Models\Project;
use Illuminate\Http\Request;

class FavoriteActionController extends Controller
{
    public function addToProject(FavoriteActionRequest $request, Favorite $favorite): FavoriteResource
    {
        abort_unless($favorite->user_id === $request->user()->id, 404);
        $project = Project::query()->whereBelongsTo($request->user())->findOrFail($request->integer('project_id'));
        $fabrics = collect($project->fabrics ?? []);

        if (! $fabrics->contains('favorite_id', $favorite->id)) {
            $fabrics->push([
                'favorite_id' => $favorite->id,
                'name' => $favorite->name,
                'collection' => $favorite->collection,
                'color' => $favorite->colors[0] ?? null,
                'image' => $favorite->image_url,
            ]);
            $project->update(['fabrics' => $fabrics->all()]);
        }

        return new FavoriteResource($favorite->load('folder:id,name'));
    }

    public function requestSample(Request $request, Favorite $favorite): FavoriteResource
    {
        abort_unless($favorite->user_id === $request->user()->id, 404);

        $favorite->update(['sample_requested_at' => now()]);

        return new FavoriteResource($favorite->refresh()->load('folder:id,name'));
    }
}
