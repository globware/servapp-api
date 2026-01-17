<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProductMessage extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userProduct()
    {
        return $this->belongsTo(UserProduct::class);
    }
}
