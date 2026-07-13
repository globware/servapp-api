<?php

namespace App\Models;

use App\Models\UserServiceRequest;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function complainer()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function accused()
    {
        if ($this->target_type === 'App\Models\UserServiceRequest') {
            $request = $this->target;
            if ($request) {
                return $this->user_id == $request->user_id
                    ? $request->userService->user
                    : $request->user;
            }
        }
        return null;
    }

    public function target()
    {
        return $this->morphTo("target");
    }

    public function reference()
    {
        return $this->morphTo("reference");
    }

    public function closedBy()
    {
        return $this->belongsTo(Admin::class, "closed_by", "id");
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (Complaint $complaint) {
            //
        });
    }
}
