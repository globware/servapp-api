<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProductMedia extends Model
{
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (UserServiceMedia $serviceMedia) {
            if($serviceMedia->file) $serviceMedia->file->delete();
        });

    }
}
