<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\IndexUserRequest;
use App\Http\Requests\Users\SaveUserRequest;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use App\Services\Users\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct(private readonly UserService $service) {}

    public function index(IndexUserRequest $request): AnonymousResourceCollection
    {
        return UserResource::collection($this->service->paginate($request->user(), $request->validated()));
    }

    public function show(Request $request, User $user): UserResource
    {
        return new UserResource($this->service->detail($request->user(), $user));
    }

    public function store(SaveUserRequest $request): JsonResponse
    {
        return (new UserResource($this->service->create($request->user(), $request->validated())))->response()->setStatusCode(201);
    }

    public function update(SaveUserRequest $request, User $user): UserResource
    {
        return new UserResource($this->service->update($request->user(), $user, $request->validated()));
    }

    public function destroy(Request $request, User $user): Response
    {
        Gate::authorize('delete', $user);
        $this->service->delete($request->user(), $user);

        return response()->noContent();
    }
}
