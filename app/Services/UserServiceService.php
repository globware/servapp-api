<?php 

namespace App\Services;

use App\Exceptions\AppException;

use App\Models\UserService;
use App\Models\Service;
use App\Models\UserServiceTag;
use App\Models\UserServiceMedia;

use App\Services\UserService as UserServiceClass;
use App\Services\ServiceService;
use App\Services\LocationService;

class UserServiceService 
{
    protected $userService;
    protected $service;
    protected $locationService;

    public $userId = null;
    public $serviceId = null;
    public $count = [];

    public function __construct()
    {
        $this->userService = new UserServiceClass;
        $this->service = new ServiceService;
        $this->locationService = new LocationService;
    }

    public function save($data)
    {
        try{
            $userService = new UserService;

            if(!isset($data['name'])) {
                $user = $this->userService->getUser($data['userId']);
                $service = $this->service->getService($data['serviceId']);
                $data['name'] = $user->firstname . ' ' . $service->name;
            }
            $location = $this->locationService->getLocation($data['locationId']);

            $userService->user_id = $data['userId'];
            $userService->service_id = $data['serviceId'];
            $userService->name = $data['name'];
            $userService->address = $data['address'];
            $userService->location_id = $data['locationId'];
            $userService->state_id = $location->state->id;
            $userService->country_id = $location->state->country->id;
            if(isset($data['long'])) $userService->longitude = $data['long'];
            if(isset($data['lat'])) $userService->latitude = $data['lat'];
            if(isset($data['phoneNumbers'])) $userService->phone_numbers = array_filter(
                array_map('trim', explode(',', $data['phoneNumbers'])),
                fn($v) => $v !== ""
            );
            if(isset($data['allDay'])) {
                $userService->all_day = true;
            }else{
                $userService->opening_time = $data['openingTime'];
                if(isset($data['closingTime'])) $userService->closing_time = $data['closingTime'];
            }
            if(isset($data['minPrice'])) $userService->min_price = $data['minPrice'];
            if(isset($data['maxPrice'])) $userService->max_price = $data['maxPrice'];
            if(isset($data['description'])) $userService->description = $data['description'];

            $userService->save();

            if(isset($data['tags'])) $this->addServiceTags($userService, $data['tags']);
            if(isset($data['mediaIds'])) $this->addMedia($userService, $data['mediaIds']);

            return $this->getService($userService->id);

            return $userService;
        } catch(\Exception $e) {
            throw $e;
            // throw new AppException(500, "An error Occurred..", $e);
        }
    }

    public function update($data, $userService)
    {
        $updated = [];
        if(isset($data['serviceId'])) {
            if($userService->service_id != $data['serviceId']) $updated[] = 'Service';
            $userService->service_id = $data['serviceId'];
        }
        if(isset($data['name'])) {
            if($userService->name != $data['name']) $updated[] = 'Name';
            $userService->name = $data['name'];
        }

        if(isset($data['coverPhotoId'])) {
            if($userService->cover_photo_id != $data['coverPhotoId']) $updated[] = 'coverPhotoId';
            $userService->cover_photo_id = $data['coverPhotoId'];
        }

        if(isset($data['address'])) {
            if($userService->address != $data['address']) $updated[] = 'Address';
            $userService->address = $data['address'];
        }

        if(isset($data['locationId'])) {
            if($userService->location_id != $data['locationId']) {
                $location = $this->locationService->getLocation($data['locationId']);
                $updated[] = 'Location';
                $userService->location_id = $data['locationId'];
                $userService->state_id = $location->state->id;
                $userService->country_id = $location->state->country->id;
            }
        }

        if(isset($data['long']) || isset($data['lat'])) {
            if($userService->long != $data['long'] || $userService->lat != $data['lat']) $updated[] = 'GPS';
            if(isset($data['long'])) $userService->long = $data['long'];
            if(isset($data['lat'])) $userService->lat = $data['lat'];
        }
        
        if(isset($data['phoneNumbers'])) $userService->phone_numbers = explode(",", $data['phoneNumbers']);
        if(isset($data['allDay'])) {
            $userService->all_day = true;
            $userService->opening_time = null;
            $userService->closing_time = null;
        }
        if(isset($data['openingTime'])){
            $userService->opening_time = $data['openingTime'];
            if(isset($data['closingTime'])) $userService->closing_time = $data['closingTime'];
            $userService->all_day = false;
        }
        if(isset($data['minPrice'])) $userService->min_price = $data['minPrice'];
        if(isset($data['maxPrice'])) $userService->max_price = $data['maxPrice'];

        if(isset($data['description'])) {
            if($userService->description != $data['description']) $updated[] = 'Description';
            $userService->description = $data['description'];
        }

        $userService->update();

        return ["service" => $userService, "updated" => $updated];
    }

    public function addServiceTags($service, $tags)
    {
        if(is_array($tags) && !empty($tags)) {
            foreach($tags as $tag) {
                $serviceTag = $this->service->AddTag($tag);
                UserServiceTag::firstOrCreate([
                    "service_tag_id" => $serviceTag->id,
                    "user_service_id" => $service->id
                ]);
            }
        }
    }

    public function removeServiceTag($serviceId, $tagId)
    {
        $service = $this->getService($serviceId);
        if(!$service) throw new AppException(402, "User Service not found");

        $serviceTag = $this->service->getServiceTag($tagId);
        if(!$serviceTag) throw new AppException(402, "Service Tag not found");

        $userServiceTag = UserServiceTag::where("service_tag_id", $tagId)->where("user_service_id", $serviceId)->first();
        if($userServiceTag) $userServiceTag->delete();
    }

    public function addMedia($service, $mediaIds)
    {
        if(is_array($mediaIds) && !empty($mediaIds)) {
            foreach($mediaIds as $mediaId) {
                UserServiceMedia::firstOrCreate([
                    "file_id" => $mediaId,
                    "user_service_id" => $service->id
                ]);
            }
        }
    }

    public function getServices($with=[])
    {
        $query = UserService::with($with)->withCount($this->count);

        if($this->userId) $query = $query->where("user_id", $this->userId);
        if($this->serviceId) $query = $query->where("service_id", $this->serviceId);

        return $query->orderBy("created_at", "DESC")->get();
    }

    public function getService($id, $with=[])
    {
        return UserService::with($with)->withCount($this->count)->where("id", $id)->first();

        // if($this->userId) $query = $query->where("user_id", $this->userId);
        // if($this->serviceId) $query = $query->where("service_id", $this->serviceId);

        // return $query->orderBy("created_at", "DESC")->get();
    }

    public function getTopServices($count=null)
    {
        return UserService::topByRequests($count)->get();
    }
}