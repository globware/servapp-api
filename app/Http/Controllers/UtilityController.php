<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exceptions\AppException;

use App\Http\Resources\StateResource;
use App\Http\Resources\LocationResource;

use App\Services\LocationService;

use App\Models\Country;

use App\Utilities;

class UtilityController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function states($countryId=null)
    {
        try{
            if(!$countryId) $countryId = Country::nigeria()->id;
            $states = $this->locationService->getStates($countryId, ['locations']);

            return Utilities::ok(StateResource::collection($states));
        } catch(AppException $e){
            throw $e;
        }
    }
}
