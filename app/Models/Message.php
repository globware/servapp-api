<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function sender()
    {
        return $this->morphTo('sender');
    }

    public function receiver()
    {
        return $this->morphTo('receiver');
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (Message $message) {
            //
        });
    }
}
