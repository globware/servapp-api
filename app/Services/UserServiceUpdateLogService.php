<?php 

namespace App\Services;

use App\Models\UserServiceUpdateLog;

use App\Helpers;

class UserServiceUpdateLogService 
{
    public function save($data)
    {
        $log = $this->getLatestUpdate($data['serviceId']);
        // dd($log);
        $updates = [];
        if(!$log) {
            $log = new UserServiceUpdateLog;
        }else{
            $updates = $log->update;
        }

        $updated = Helpers::joinWords($data['updates']);

        $update = $updated." were updated ";

        $updates[] = $update;

        $log->user_service_id = $data['serviceId'];
        $log->update = $updates;
        $log->save();

        return $log;
    }

    public function getLatestUpdate($userServiceId)
    {
        // dd($userServiceId);
        return UserServiceUpdateLog::where("user_service_id", $userServiceId)->where(function($query) {
            $query->whereNull("approved")->orWhere("approved", false);
        })->first();
    }
}