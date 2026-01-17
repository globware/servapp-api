<?php

namespace App\Services;

use App\Models\User;

use App\Exceptions\AppException;

class ProviderService
{
    public $count = [];

    public function providers($with=[])
    {
        return User::with($with)->withCount($this->count)->whereHas("userServices")->get();
    }


}