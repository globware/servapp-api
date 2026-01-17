<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\UserService;
use App\Models\UserProduct;

class Review extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function target()
    {
        return $this->morphTo();
    }

    public function service()
    {
        return ($this->target->getType() == UserService::$type) ? $this->target->service : null;
    }

    public function product()
    {
        return ($this->target->getType() == UserProduct::$type) ? $this->target->product : null;
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (ServiceReview $review) {
            //
        });
    }
}
