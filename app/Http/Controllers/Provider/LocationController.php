<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ProviderLocationService;
use App\Http\Requests\Provider\StoreProviderLocationRequest;
use App\Http\Requests\Provider\UpdateProviderLocationRequest;
use App\Http\Resources\ProviderLocationResource;
use App\Utilities;

class LocationController extends Controller
{
    public function __construct(protected ProviderLocationService $locationService)
    {
    }

    public function index(Request $request)
    {
        $locations = $this->locationService->getLocations(Auth::id());
        return Utilities::ok(ProviderLocationResource::collection($locations));
    }

    public function store(StoreProviderLocationRequest $request)
    {
        try {
            $location = $this->locationService->save($request->validated(), Auth::id());
            return Utilities::ok(new ProviderLocationResource($location));
        } catch (\Exception $e) {
            return Utilities::error($e, "Failed to create provider location");
        }
    }

    public function show($id)
    {
        $location = $this->locationService->getLocation($id, Auth::id());
        if (!$location) {
            return Utilities::error402("Location not found");
        }

        return Utilities::ok(new ProviderLocationResource($location));
    }

    public function update(UpdateProviderLocationRequest $request, $id)
    {
        try {
            $location = $this->locationService->update($id, $request->validated(), Auth::id());
            return Utilities::ok(new ProviderLocationResource($location));
        } catch (\Exception $e) {
            return Utilities::error($e, "Failed to update provider location");
        }
    }

    public function destroy($id)
    {
        try {
            $this->locationService->delete($id, Auth::id());
            return Utilities::okay("Location deleted successfully");
        } catch (\Exception $e) {
            return Utilities::error($e, "Failed to delete provider location");
        }
    }
}
