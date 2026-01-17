<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserServiceMedia extends Model
{
    protected $fillable = ["file_id", "user_service_id"];

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
