<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class UnclaimedLead extends Model
{
    use Searchable;

    protected $guarded = [];

    public function getTitleAttribute($value)
    {
        return $value ?: $this->business_name;
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['business_name'])) {
            $this->attributes['business_name'] = $value;
        }
    }

    public function setBusinessNameAttribute($value)
    {
        $this->attributes['business_name'] = $value;
        if (empty($this->attributes['title'])) {
            $this->attributes['title'] = $value;
        }
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->title, // mapped to name for unified search
            'description' => $this->description,
            'lead_type' => $this->lead_type, // 'service' or 'product'
            'category_text' => $this->category_text,
            'address' => $this->address,
            'is_unclaimed' => true,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'approved' => (bool) $this->approved,
        ];
    }
}
