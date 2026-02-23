<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\UserServiceService;

use App\Utilities;

use App\Exceptions\AppException;

class ServiceController extends Controller
{
    protected $userServiceService;

    public function __construct(UserServiceService $userServiceService)
    {
        $this->userServiceService = $userServiceService;
    }

    public function index()
    {
        $services = $this->userServiceService->getServices();
        return view('admin.services.index', compact('services'));
    }

    public function show($id)
    {
        $service = $this->userServiceService->getService($id);
        return view('admin.services.show', compact('service'));
    }

    public function verify($id)
    {
        try{
            $userService = $this->userServiceService->verify($id);
            return Utilities::ok([
                'success' => true,
                'message' => 'Service verified successfully.'
            ]);
        }catch(\Exception $e){
            if ($e instanceof AppException) {
                throw $e;
            }

            return Utilities::error($e, 'An Error Occurred while attempting to verify this service');
        }
    }

    public function approve($id)
    {

    }
}
