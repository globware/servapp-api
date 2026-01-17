<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;
    
    public function userServiceMedia()
    {
        return $this->hasMany(UserServiceMedia::class);
    }    

    public static function boot ()
    {
        parent::boot();

        self::deleting(function (File $file) {
            Storage::disk($file->disk)->delete($file->url); 
        });

        self::deleted(function (File $file) {
            if($file->userServiceMedia->count() > 0) {
                foreach($file->userServiceMedia as $serviceMedia) {
                    // dd($serviceMedia);
                    $serviceMedia->delete();
                }
            }
            // dd('deleted service media');
        });
    }
}
