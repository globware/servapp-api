<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
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
