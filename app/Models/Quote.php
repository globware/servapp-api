<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $guarded = [];

    public function serviceRequest()
    {
        return $this->belongsTo(UserServiceRequest::class, 'user_service_request_id');
    }
}
