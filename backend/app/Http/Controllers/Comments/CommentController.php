<?php

namespace App\Http\Controllers\Comments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comments\StoreCommentRequest;
use App\Http\Resources\Comments\CommentResource;
use App\Models\Task;
use App\Services\Comments\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(private readonly CommentService $service) {}

    public function store(StoreCommentRequest $request, Task $task): JsonResponse
    {
        return (new CommentResource($this->service->create($request->user(), $task, $request->validated())))
            ->response()->setStatusCode(201);
    }
}
