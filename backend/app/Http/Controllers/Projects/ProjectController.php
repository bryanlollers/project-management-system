<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\IndexProjectRequest;
use App\Http\Requests\Projects\SaveProjectRequest;
use App\Http\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Services\Projects\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function __construct(private readonly ProjectService $service) {}

    public function index(IndexProjectRequest $request): AnonymousResourceCollection
    {
        return ProjectResource::collection($this->service->paginate($request->user(), $request->validated()));
    }

    public function show(Request $request, Project $project): ProjectResource
    {
        return new ProjectResource($this->service->detail($request->user(), $project));
    }

    public function store(SaveProjectRequest $request): JsonResponse
    {
        return (new ProjectResource($this->service->create($request->user(), $request->validated())))->response()->setStatusCode(201);
    }

    public function update(SaveProjectRequest $request, Project $project): ProjectResource
    {
        return new ProjectResource($this->service->update($request->user(), $project, $request->validated()));
    }

    public function destroy(Request $request, Project $project): Response
    {
        Gate::authorize('delete', $project);
        $this->service->delete($request->user(), $project);

        return response()->noContent();
    }
}
