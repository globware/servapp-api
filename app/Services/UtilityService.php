<?php 

namespace App\Services;

use App\Models\ServiceTag;

class UtilityService 
{

    public function getServiceTag($id)
    {
        return ServiceTag::find($id);
    }

    public function getServiceTags()
    {
        return ServiceTag::OrderBy("created_at")->get();
    }

}