<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\DashboardResource;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $service) {}

    public function show(Request $request): JsonResponse
    {
        // Keep the reporting payload unwrapped for the existing frontend contract.
        return response()->json((new DashboardResource($this->service->summary($request->user())))->resolve($request));
    }
}
