<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    /**
     * Get all services
     * @return JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $services = Service::all();
        return response()->json($services, Response::HTTP_OK);
    }

    /**
     * Get service by ID
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($service, Response::HTTP_OK);
    }

    /**
     * Create new service
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $requestData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $service = Service::create($requestData);

        return response()->json($service, Response::HTTP_CREATED);
    }

    /**
     * Update service
     * @param string $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(string $id, Request $request): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        $requestData = $request->validate([
            'name' => 'string|max:255',
            'price' => 'numeric',
        ]);

        $service->update($requestData);

        return response()->json($service, Response::HTTP_OK);
    }
    /**
     * Delete service
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        $service->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
