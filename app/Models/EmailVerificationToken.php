<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailVerificationToken extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'email',
        'user_id',
        'token_signature',
        'expires_at',
        'verified'
    ];
}
