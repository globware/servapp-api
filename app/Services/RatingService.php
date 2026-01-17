<?php 

namespace App\Services;

use App\Exceptions\AppException;

use App\Models\Rating;

class RatingService 
{
    public function save($data)
    {
        try{
            $rating = Rating::where("user_id", $data['userId'])->where("target_id", $data['targetId'])->where("target_type", $data['targetType'])
                        ->where("user_service_request_id", $data['requestId'])->first();
            if($rating) throw new AppException(402, "You have already rated this service");

            $rating = new Rating; 
            $rating->user_id = $data['userId'];
            $rating->target_id = $data['targetId'];
            $rating->target_type = $data['targetType'];
            $rating->user_service_request_id = $data['requestId'];
            $rating->ratings = $data['rating'];
            $rating->save();

            return $rating;
        } catch(\Exception $e) {
            throw new AppException(500, "An Error occurred when saving a rating", $e);
        }
    }

    public function ratings($targetId, $targetType)
    {
        return Rating::where("target_id", $targetId)->where("target_type", $targetType)->orderBy("created_at", "DESC")->get();
    }

}