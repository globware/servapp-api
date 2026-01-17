<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Identification extends Model
{
    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (Identification $identification) {
            if($identification->users->count() > 0) {
                foreach($identification->users as $user){
                    $user->identification_id = null;
                    $user->update();
                }
            } 
        });
    }
}
