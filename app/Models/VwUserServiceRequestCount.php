<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VwUserServiceRequestCount extends Model
{
    protected $table = 'vw_user_service_request_counts';

    public $timestamps = false;

    protected $guarded = [];

    public $incrementing = false;
}
