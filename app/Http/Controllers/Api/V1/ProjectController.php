<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = $request->user()->projects()
            ->when(! $request->boolean('include_archived'), fn ($query) => $query->visible())
            ->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->string('search').'%')
                    ->orWhere('client', 'like', '%'.$request->string('search').'%');
            }))
            ->orderBy('id')
            ->paginate($request->integer('per_page', 4));

        return ProjectResource::collection($projects);
    }

    public function store(UpsertProjectRequest $request): ProjectResource
    {
        $data = $request->safe()->except('cover_image_file');
        if ($request->hasFile('cover_image_file')) {
            $data['cover_image'] = $request->file('cover_image_file')->store('projects/covers', 'public');
        }

        return new ProjectResource($request->user()->projects()->create($data));
    }

    public function show(Request $request, Project $project): ProjectResource
    {
        $this->ensureOwned($request, $project);

        return new ProjectResource($project);
    }

    public function update(UpsertProjectRequest $request, Project $project): ProjectResource
    {
        $this->ensureOwned($request, $project);
        $data = $request->safe()->except('cover_image_file');
        if ($request->hasFile('cover_image_file')) {
            $this->deleteFile($project->cover_image);
            $data['cover_image'] = $request->file('cover_image_file')->store('projects/covers', 'public');
        }
        $project->update($data);

        return new ProjectResource($project->refresh());
    }

    public function destroy(Request $request, Project $project): JsonResponse
    {
        $this->ensureOwned($request, $project);
        $this->deleteFile($project->cover_image);
        foreach ($project->inspiration_images ?? [] as $path) {
            $this->deleteFile($path);
        }
        $project->delete();

        return response()->json(status: 204);
    }

    public function favorite(Request $request, Project $project): ProjectResource
    {
        $this->ensureOwned($request, $project);
        $project->update(['is_favorite' => ! $project->is_favorite]);

        return new ProjectResource($project->refresh());
    }

    public function archive(Request $request, Project $project): ProjectResource
    {
        $this->ensureOwned($request, $project);
        $project->update(['archived_at' => $project->archived_at ? null : now()]);

        return new ProjectResource($project->refresh());
    }

    public function duplicate(Request $request, Project $project): ProjectResource
    {
        $this->ensureOwned($request, $project);
        $copy = $project->replicate(['archived_at']);
        $copy->name = $project->name.' — Copy';
        $copy->is_favorite = false;
        $copy->recent_activity = [['text' => 'Project duplicated', 'time' => 'Just now']];
        $copy->save();

        return new ProjectResource($copy);
    }

    private function ensureOwned(Request $request, Project $project): void
    {
        abort_unless($project->user_id === $request->user()->getKey(), 404);
    }

    private function deleteFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http') && ! str_starts_with($path, '/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
