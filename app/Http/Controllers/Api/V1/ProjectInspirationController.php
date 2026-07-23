<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectInspirationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Project $project): ProjectResource
    {
        abort_unless($project->user_id === $request->user()->getKey(), 404);
        $request->validate(['image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']]);
        $images = $project->inspiration_images ?? [];
        $images[] = $request->file('image')->store('projects/inspiration', 'public');
        $project->update(['inspiration_images' => $images]);

        return new ProjectResource($project->refresh());
    }
}
