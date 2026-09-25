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

    public function media()
    {
        return $this->belongsToMany(File::class, 'chat_media', 'chat_id', 'file_id');
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (Chat $chat) {
            //
        });
    }
}
