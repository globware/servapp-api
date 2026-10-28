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
use App\Services\ProviderLocationService;

class UserServiceService 
{
    protected $userService;
    protected $service;
    protected $locationService;
    protected $providerLocationService;

    public $userId = null;
    public $serviceId = null;
    public $count = [];
    public $getCount = false;
    public $limit = null;
    public $page = 1;
    public $paginated = false;
    public $approved = true;
    public $active = true;

    public function __construct()
    {
        $this->userService = new UserServiceClass;
        $this->service = new ServiceService;
        $this->locationService = new LocationService;
        $this->providerLocationService = new ProviderLocationService;
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
            if (!empty($data['providerLocationId'])) {
                $providerLoc = $this->providerLocationService->getLocation($data['providerLocationId'], $data['userId']);
                if (!$providerLoc) {
                    throw new AppException(402, "Selected provider location not found or unauthorized.");
                }
                $userService->provider_location_id = $providerLoc->id;
                $userService->address = $data['address'] ?? $providerLoc->address;
                $userService->location_id = $data['locationId'] ?? $providerLoc->location_id;
                $userService->state_id = $providerLoc->state_id;
                $userService->country_id = $providerLoc->country_id;
                $userService->latitude = $data['lat'] ?? $providerLoc->latitude;
                $userService->longitude = $data['long'] ?? $providerLoc->longitude;
            } else {
                $location = $this->locationService->getLocation($data['locationId']);
                $userService->address = $data['address'];
                $userService->location_id = $data['locationId'];
                $userService->state_id = $location?->state?->id;
                $userService->country_id = $location?->state?->country?->id;
                if(isset($data['long'])) $userService->longitude = $data['long'];
                if(isset($data['lat'])) $userService->latitude = $data['lat'];

                if (!empty($data['saveLocation'])) {
                    $savedLoc = $this->providerLocationService->save([
                        'title' => $data['locationTitle'] ?? null,
                        'address' => $data['address'],
                        'latitude' => $userService->latitude,
                        'longitude' => $userService->longitude,
                        'country_id' => $userService->country_id,
                        'state_id' => $userService->state_id,
                        'location_id' => $userService->location_id,
                    ], $data['userId']);
                    $userService->provider_location_id = $savedLoc->id;
                }
            }

            $userService->user_id = $data['userId'];
            $userService->service_id = $data['serviceId'];
            $userService->name = $data['name'];
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

        if (isset($data['providerLocationId'])) {
            $providerLoc = $this->providerLocationService->getLocation($data['providerLocationId'], $userService->user_id);
            if (!$providerLoc) {
                throw new AppException(402, "Selected provider location not found or unauthorized.");
            }
            $userService->provider_location_id = $providerLoc->id;
            $userService->address = $providerLoc->address;
            $userService->location_id = $providerLoc->location_id;
            $userService->state_id = $providerLoc->state_id;
            $userService->country_id = $providerLoc->country_id;
            $userService->latitude = $providerLoc->latitude;
            $userService->longitude = $providerLoc->longitude;
            $updated[] = 'Location';
        } else {
            if(isset($data['address'])) {
                if($userService->address != $data['address']) $updated[] = 'Address';
                $userService->address = $data['address'];
            }

            if(isset($data['locationId'])) {
                if($userService->location_id != $data['locationId']) {
                    $location = $this->locationService->getLocation($data['locationId']);
                    $updated[] = 'Location';
                    $userService->location_id = $data['locationId'];
                    $userService->state_id = $location?->state?->id;
                    $userService->country_id = $location?->state?->country?->id;
                }
            }

            if(isset($data['long']) || isset($data['lat'])) {
                if($userService->longitude != ($data['long'] ?? $userService->longitude) || $userService->latitude != ($data['lat'] ?? $userService->latitude)) $updated[] = 'GPS';
                if(isset($data['long'])) $userService->longitude = $data['long'];
                if(isset($data['lat'])) $userService->latitude = $data['lat'];
            }

            if (!empty($data['saveLocation']) && !empty($userService->address)) {
                $savedLoc = $this->providerLocationService->save([
                    'title' => $data['locationTitle'] ?? null,
                    'address' => $userService->address,
                    'latitude' => $userService->latitude,
                    'longitude' => $userService->longitude,
                    'country_id' => $userService->country_id,
                    'state_id' => $userService->state_id,
                    'location_id' => $userService->location_id,
                ], $userService->user_id);
                $userService->provider_location_id = $savedLoc->id;
                $updated[] = 'SavedLocation';
            }
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
        $limit = $this->limit;
        if($this->paginated) {
            if(!$limit) $limit = env('PAGINATION_PER_PAGE', 10);

            $perPage = min(100, max(1, $limit));
        }

        $query = UserService::with($with)->withCount($this->count);

        if($this->userId) $query = $query->where("user_id", $this->userId);
        if($this->serviceId) $query = $query->where("service_id", $this->serviceId);
        if($this->approved) $query = $query->where("approved", $this->approved);
        if($this->active) $query = $query->where("active", $this->active);

        if($this->getCount) return $query->count();

        $query = $query->orderBy("created_at", "DESC");

        if (!$this->paginated && $this->limit) {
            $query->limit($this->limit);
        }

        if($this->paginated) return $query->paginate($perPage, ['*'], 'page', $this->page);

        return $query->get();
    }

    public function getByLocation(array $params = [], array $with = [])
    {
        $lat = $params['lat'] ?? null;
        $long = $params['long'] ?? null;
        $radius = (float) ($params['radius'] ?? 25);
        $locationId = $params['location_id'] ?? null;
        $serviceId = $params['service_id'] ?? null;

        $query = UserService::with($with)->withCount($this->count);

        if ($this->approved !== null) {
            $query->where('approved', $this->approved);
        }
        if ($this->active !== null) {
            $query->where('active', $this->active);
        }
        $query->where('suspended', false);

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        if ($lat && $long) {
            $haversine = "(6371 * acos(
                cos(radians(?)) *
                cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) *
                sin(radians(latitude))
            ))";

            $query->selectRaw("user_services.*, {$haversine} AS distance", [$lat, $long, $lat])
                  ->whereNotNull('latitude')
                  ->whereNotNull('longitude')
                  ->whereRaw("{$haversine} <= ?", [$lat, $long, $lat, $radius])
                  ->orderBy('distance', 'ASC');
        } elseif ($locationId) {
            $query->where('location_id', $locationId)
                  ->orderBy('created_at', 'DESC');
        } else {
            $query->orderBy('created_at', 'DESC');
        }

        $limit = $this->limit ?? (int) env('PAGINATION_PER_PAGE', 10);
        $perPage = min(100, max(1, $limit));

        if ($this->paginated) {
            return $query->paginate($perPage, ['*'], 'page', $this->page);
        }

        if ($this->limit) {
            $query->limit($this->limit);
        }

        return $query->get();
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
        return UserService::topByRequests($count)->where("approved", true)->get();
    }

    public function verify($id)
    {
        try{
            $userService = $this->getService($id);
            if(!$userService) throw new AppException(402, "This User Service does not exist");

            $userService->verified = true;
            $userService->approved = true;
            $userService->update();
            return $userService;
        }catch(\Exception $e){
            throw new AppException(500, "An Error Occurred while attempting to verify this service", $e);
        }
    }

    public function toggleActivate($id)
    {
        try{
            $userService = $this->getService($id);
            if(!$userService) throw new AppException(402, "This User Service does not exist");

            $userService->active = !$userService->active;
            $userService->update();
            return $userService;
        }catch(\Exception $e){
            if($userService) {
                $action = ($userService->active) ? "activate" : "deactivate";
                throw new AppException(500, "An Error Occurred while attempting to ".$action." this service", $e);
            }else{
                throw new AppException(500, "An Error Occurred while attempting to toggle this service active status", $e);
            }
        }
    }

    public function approve($id)
    {
        try{
            $userService = $this->getService($id);
            if(!$userService) throw new AppException(402, "This User Service does not exist");

            $userService->approved = true;
            $userService->update();
        }catch(\Exception $e){
            throw new AppException(500, "An Error Occurred while attempting to approve this service", $e);
        }
    }

    public function delete($userService)
    {
        try{
            if($userService->openComplaints()->count() > 0) throw new AppException(402, "sorry, you cannot delete this service at the moment");

            $userService->delete();
        }catch(\Exception $e){
            throw new AppException(500, "An Error Occurred while attempting to verify this service", $e);
        }
    }
}