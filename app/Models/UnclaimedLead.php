<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class UnclaimedLead extends Model
{
    use Searchable;

    protected $guarded = [];

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
