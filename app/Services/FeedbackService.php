<?php 

namespace App\Services;

use App\Exceptions\AppException;

use App\Models\UserFeedback;

class FeedbackService
{
    public function save(array $data)
    {
        $feedback = new UserFeedback;
        $feedback->user_id = $data['userId'];
        $feedback->target_type = $data['targetType'];
        $feedback->target_id = $data['targetId'];
        if(isset($data['rating'])) $feedback->rating = $data['rating'];
        if(isset($data['review'])) $feedback->review = $data['review'];
        $feedback->save();

        return $feedback;
    }
}