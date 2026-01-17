<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exceptions\AppException;

use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserServiceResource;
use App\Http\Resources\UserResource;

use App\Services\ServiceService;
use App\Services\UserServiceService;
use App\Services\UserService;

use App\Utilities;

class IndexController extends Controller
{
    protected $serviceService;
    protected $userServiceService;
    protected $userService;

    public function __construct(ServiceService $serviceService, UserServiceService $userServiceService, UserService $userService)
    {
        $this->serviceService = $serviceService;
        $this->userServiceService = $userServiceService;
        $this->userService = $userService;
    }

    public function dashboard(Request $request)
    {
        $long = $request->query("long");
        $lat = $request->query("lat");

        $this->serviceService->limit = 5;
        $this->serviceService->approved = true;
        $services = collect([]);
        if($long && $lat) {
            $services = $this->serviceService->getByGps($long, $lat);
        }

        if($services->count() == 0) {
            $services = $this->serviceService->getByLocation(Auth::user()->location_id);

            if($services->count() == 0) {
                $services = $this->serviceService->getServices();
            }
        }

        $topServices = $this->userServiceService->getTopServices(4);

        return Utilities::ok([
            "services" => ServiceResource::collection($services),
            "topServices" => UserServiceResource::collection($topServices)
        ]);
    }

    public function loggedInUser()
    {
        $this->userService->count = ['inbox'];
        $user = $this->userService->getUser(Auth::user()->id);

        return Utilities::ok(new UserResource($user));
    }
}
