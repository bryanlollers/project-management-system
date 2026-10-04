<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clients\IndexClientRequest;
use App\Http\Requests\Clients\SaveClientRequest;
use App\Http\Resources\Clients\ClientResource;
use App\Models\Client;
use App\Services\Clients\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller
{
    public function __construct(private readonly ClientService $service) {}

    public function index(IndexClientRequest $request): AnonymousResourceCollection
    {
        return ClientResource::collection($this->service->paginate($request->user(), $request->validated()));
    }

    public function show(Request $request, Client $client): ClientResource
    {
        return new ClientResource($this->service->detail($request->user(), $client));
    }

    public function store(SaveClientRequest $request): JsonResponse
    {
        return (new ClientResource($this->service->create($request->user(), $request->validated())))->response()->setStatusCode(201);
    }

    public function update(SaveClientRequest $request, Client $client): ClientResource
    {
        return new ClientResource($this->service->update($request->user(), $client, $request->validated()));
    }

    public function destroy(Request $request, Client $client): Response
    {
        Gate::authorize('delete', $client);
        $this->service->delete($request->user(), $client);

        return response()->noContent();
    }
}
