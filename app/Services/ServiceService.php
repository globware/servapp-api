<?php 

namespace App\Services;

use App\Models\Service;
use App\Models\UserService;
use App\Models\Location;
use App\Models\ServiceTag;

class ServiceService
{
    public $count=[];
    public $limit = null;
    public $approved = null;

    public function getService($id, $with=[])
    {
        $query = Service::with($with)->withCount($this->count);
        
        return $query->where("id", $id)->first();
    }

    public function getServices($with=[])
    {
        $query = Service::with($with)->withCount($this->count);

        if($this->approved != null) $query = $query->where("approved", $this->approved);
        if($this->limit) $query = $query->limit($this->limit);
        
        return $query->orderBy("name")->get();
    }

    public function getByLocation($locationId, $with=[])
    {
        $query = Service::with($with)->withCount($this->count)->whereHas("userServices", function($q) use($locationId) {
            $q->where("location_id", $locationId);
        })->orderBy("name");
        if($this->approved != null) $query = $query->where("approved", $this->approved);
        if($this->limit) $query = $query->limit($this->limit);
        return $query->get();
    }

    public function getByGps($long, $lat)
    {
        $locationIds = Location::near($long, $lat, 10)->pluck('id');

        if ($locationIds->isEmpty()) {
            return collect(); // no nearby locations
        }

        // 2. Get all services linked to user_services in those locations
        $query = Service::whereHas('userServices', function ($query) use ($locationIds) {
                $query->whereIn('location_id', $locationIds);
            })
            ->with([
                'userServices' => function ($query) use ($locationIds) {
                    $query->whereIn('location_id', $locationIds);
                }
            ]);

        if($this->approved != null) $query = $query->where("approved", $this->approved);
        if($this->limit) $query = $query->limit($this->limit);
        return $query->get();
    }

    public function getServiceTag($id)
    {
        return ServiceTag::find($id);
    }

    public function getServiceTags()
    {
        return ServiceTag::OrderBy("created_at")->get();
    }

    public function AddTag($name)
    {
        return ServiceTag::firstOrCreate(["name" => $name]);
    }
}