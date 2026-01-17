<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserServiceUpdateLog extends Model
{
    protected function casts(): array
    {
        return [
            'update' => 'array',
        ];
    }
}
