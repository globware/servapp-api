<?php 

namespace App;

use App\Enums\UserType;
use App\Enums\FileType;

class EnumClass 
{
    public static function userTypes()
    {
        return array_column(UserType::cases(), 'value');
    }

    public static function fileTypes()
    {
        return array_column(FileType::cases(), 'value');
    }
}