<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VwUserServiceRequestCountByUser extends Model
{
    protected $table = 'vw_user_service_request_counts_by_user';

    public $timestamps = false;

    protected $guarded = [];

    public $incrementing = false;
}
