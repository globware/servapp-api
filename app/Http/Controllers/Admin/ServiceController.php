<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\UserServiceService;

class ServiceController extends Controller
{
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
        $service = $this->userServiceService->getServices($id);
        return view('admin.services.show', compact('service'));
    }
}
