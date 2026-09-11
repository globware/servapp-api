<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    public function requestable()
    {
        return $this->morphTo();
    }

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

        static::deleting(function (Chat $chat) {
            //
        });
    }
}
