<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserServiceTag extends Model
{
    protected $fillable = ["service_tag_id", "user_service_id"];
}
