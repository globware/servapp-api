<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProductRequest extends Model
{
    public static $type = "App\Models\UserProductRequest";
    
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userProduct()
    {
        return $this->belongsTo(UserProduct::class);
    }

    public function chats()
    {
        return $this->morphMany(Chat::class, 'requestable')->orderBy("created_at", "DESC");
    }
}
