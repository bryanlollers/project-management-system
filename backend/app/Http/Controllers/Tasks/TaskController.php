<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\IndexTaskRequest;
use App\Http\Requests\Tasks\SaveTaskRequest;
use App\Http\Resources\Tasks\TaskResource;
use App\Models\Task;
use App\Services\Tasks\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $service) {}

    public function index(IndexTaskRequest $request): AnonymousResourceCollection
    {
        return TaskResource::collection($this->service->paginate($request->user(), $request->validated()));
    }

    public function show(Request $request, Task $task): TaskResource
    {
        return new TaskResource($this->service->detail($request->user(), $task));
    }

    public function store(SaveTaskRequest $request): JsonResponse
    {
        return (new TaskResource($this->service->create($request->user(), $request->validated())))->response()->setStatusCode(201);
    }

    public function update(SaveTaskRequest $request, Task $task): TaskResource
    {
        return new TaskResource($this->service->update($request->user(), $task, $request->validated()));
    }

    public function destroy(Request $request, Task $task): Response
    {
        Gate::authorize('delete', $task);
        $this->service->delete($request->user(), $task);

        return response()->noContent();
    }
}
