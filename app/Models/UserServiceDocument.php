<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserServiceDocument extends Model
{
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (UserServiceDocument $serviceDocument) {
            if($serviceDocument->file) $serviceDocument->file->delete();
        });

    }
}
