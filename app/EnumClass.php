<?php 

namespace App;

use App\Enums\UserType;
use App\Enums\FileType;
use App\Enums\ServiceRequestStatus;
use App\Enums\PasswordType;

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

    public static function passwordTypes()
    {
        return array_column(PasswordType::cases(), 'value');
    }

    public static function serviceRequestStatuses()
    {
        return array_column(ServiceRequestStatus::cases(), 'value');
    }
}