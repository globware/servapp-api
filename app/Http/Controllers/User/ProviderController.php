<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\ProviderService;

use App\Http\Resources\UserResource;

use App\Utilities;

class ProviderController extends Controller
{
    protected $providerService;

    public function __construct(ProviderService $providerService)
    {
        $this->providerService = $providerService;
    }

    public function providers()
    {
        $this->providerService->count = ['userServices'];
        $providers = $this->providerService->providers();

        return Utilities::ok(UserResource::collection($providers));
    }
}
