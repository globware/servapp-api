<?php

namespace App\Services;

use App\Models\Location;
use App\Models\State;

use App\Exceptions\AppException;

class LocationService
{
    public function getLocation($id)
    {
        return Location::find($id);
    }

    public function getStates($countryId, $with=[])
    {
        try{
            return State::with($with)->where("country_id", $countryId)->orderBy("name", "ASC")->get();
        }catch(\Exception $e){
            // $errorCode = AppException::getDefaultErrorCode(402);
            throw new AppException(500, null, $e);
        }
    }

    public function getLocations($stateId)
    {
        try{
            return Location::where("state_id", $stateId)->orderBy("name", "ASC")->get();
        } catch(\Exception $e){
            throw new AppException(500, null, $e);
        }
    }
}