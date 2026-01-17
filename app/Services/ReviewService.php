<?php 

namespace App\Services;

use App\Exceptions\AppException;

use App\Models\Review;

class ReviewService 
{
    public function save($data)
    {
        try{
            $review = new Review; 
            $review->user_id = $data['userId'];
            $review->target_id = $data['targetId'];
            $review->target_type = $data['targetType'];
            $review->review = $data['review'];
            $review->save();

            return $review;
        } catch(\Exception $e) {
            throw new AppException(500, "An Error occurred when saving a review", $e);
        }
    }

    public function reviews($targetId, $targetType)
    {
        return Review::where("target_id", $targetId)->where("target_type", $targetType)->orderBy("created_at", "DESC")->get();
    }

}