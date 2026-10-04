<?php

namespace App\Http\Controllers\Activity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Activity\IndexActivityRequest;
use App\Http\Resources\Activity\ActivityResource;
use App\Services\Activity\ActivityService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityController extends Controller
{
    public function __construct(private readonly ActivityService $service) {}

    public function index(IndexActivityRequest $request): AnonymousResourceCollection
    {
        return ActivityResource::collection($this->service->paginate($request->user(), $request->validated()));
    }
}
