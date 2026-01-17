<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTag extends Model
{
    protected $fillable = ["name"];

    /**
     * Get all user services that have this service tag
     */
    public function userServices()
    {
        return $this->belongsToMany(
            UserService::class,
            'user_service_tags',  // pivot table name
            'service_tag_id',     // foreign key for this model in pivot
            'user_service_id'     // foreign key for related model in pivot
        );
    }

    /**
     * Get all users through their user services
     */
    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            UserService::class,
            'service_tag_id',    // Foreign key on UserServiceTag table
            'id',                // Foreign key on users table
            'id',                // Local key on service_tags table
            'user_id'            // Local key on user_services table
        )->distinct();
    }
}
