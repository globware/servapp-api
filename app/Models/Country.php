<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;
    
    public static function nigeria()
    {
        return self::where("name", "Nigeria")->first();
    }

    public function userServices()
    {
        return $this->hasMany(UserService::class);
    }

    public function states()
    {
        return $this->hasMany(State::class);
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (Country $country) {
            if($country->userServices->count() > 0) {
                foreach($country->userServices as $service) {
                    $service->country_id = null;
                    $service->update();
                }
            }

            if($country->states->count() > 0) {
                foreach($country->states as $state) $state->delete();
            }
        });
    }
}
