<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\SessionResource;
use App\Http\Resources\Users\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $service) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $session = $this->service->login($request->validated());

        return response()->json((new SessionResource($session))->resolve($request));
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => (new UserResource($request->user()))->resolve($request)]);
    }

    public function logout(Request $request): Response
    {
        $this->service->logout($request->user());

        return response()->noContent();
    }
}
