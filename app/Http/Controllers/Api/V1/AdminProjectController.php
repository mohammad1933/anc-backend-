<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = Project::query()->with('user:id,name,email')
            ->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->string('archive')->value() === 'current', fn ($query) => $query->whereNull('archived_at'))
            ->when($request->string('archive')->value() === 'archived', fn ($query) => $query->whereNotNull('archived_at'))
            ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where(function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';
                $query->where('name', 'like', $search)->orWhere('client', 'like', $search)
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', $search)->orWhere('email', 'like', $search));
            }))
            ->latest('updated_at')->paginate($request->integer('per_page', 100));

        return AdminProjectResource::collection($projects);
    }

    public function show(Project $project): AdminProjectResource
    {
        return new AdminProjectResource($project->load('user:id,name,email'));
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json(status: 204);
    }
}
