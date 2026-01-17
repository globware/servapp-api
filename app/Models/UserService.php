<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Helpers;

class UserService extends Model
{
    use HasFactory;
    
    public static $type = "App\Models\UserService";
    // public $type = "App\Models\UserService";

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'phone_numbers' => 'array',
        ];
    }

    protected function startTime(): Attribute
    {
        return Attribute::make(
            get: fn($value) => \Carbon\Carbon::parse($value)->format('h:i A'), // Get as 12-hour
            set: fn($value) => \Carbon\Carbon::parse($value)->format('H:i:s'), // Store as 24-hour
        );
    }

    public function getType()
    {
        return Self::$type;
    }

    // Scopes

    /**
     * Scope to get services with most requests.
     */
    public function scopeTopByRequests($query, $limit = 10)
    {
        return $query->withCount('requests')
            ->orderBy('requests_count', 'desc')
            ->limit($limit);
    }

    /**
     * Scope to get services with pending requests.
     */
    public function scopeWithPendingRequests($query)
    {
        return $query->whereHas('requests', function ($q) {
            $q->where('status', 'pending');
        });
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function userRatings()
    {
        return $this->morphMany(Rating::class, 'target');
    }

    public function inbox()
    {
        return $this->morphMany(Message::class, "receiver");
    }

    public function outbox()
    {
        return $this->morphMany(Message::class, "sender");
    }

    public function serviceMedia()
    {
        return $this->hasMany(UserServiceMedia::class);
    }

    public function media()
    {
        return $this->belongsToMany(File::class, 'user_service_media')
                    ->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(ServiceTag::class, 'user_service_tags')
                    ->withTimestamps();
    }

    public function serviceDocuments()
    {
        return $this->hasMany(UserServiceDocument::class);
    }

    public function documents()
    {
        return $this->belongsToMany(File::class, 'user_service_documents')
                    ->withTimestamps();
    }

    public function rating()
    {
        if($this->userRatings->count() > 0) {
            $ratingsArr = [];
            foreach($this->userRatings as $rating) $ratingsArr[] = $rating->ratings;
            $average = Helpers::getAverage($ratingsArr);
            return ($average) ? $average : $this->ratings;
        }
        return $this->ratings;
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'target');
    }

    public function patronizers()
    {
        return $this->morphMany(Patronizer::class, 'target');
    }

    public function requests()
    {
        return $this->hasMany(UserServiceRequest::class, "user_service_id", "id");
    }

    /**
     * Get all service tags for this user service
     */
    public function serviceTags()
    {
        return $this->belongsToMany(
            ServiceTag::class,
            'user_service_tags',  // pivot table name
            'user_service_id',    // foreign key for this model in pivot
            'service_tag_id'      // foreign key for related model in pivot
        );
    }

    /**
     * Get the pivot records (if you need to access UserServiceTag directly)
     */
    public function userServiceTags()
    {
        return $this->hasMany(UserServiceTag::class);
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (UserService $service) {
            if($service->userRatings->count() > 0) {
                foreach($service->userRatings as $rating) $rating->delete();
            }

            if($service->reviews->count() > 0) {
                foreach($service->reviews as $review) $review->delete();
            }

            if($service->patronizers->count() > 0) {
                foreach($service->patronizers as $patronizer) $patronizer->delete();
            }

            if($service->requests->count() > 0) {
                foreach($service->requests as $request) $request->delete();
            }

            if($service->serviceMedia->count() > 0) {
                foreach($service->serviceMedia as $media) $media->delete();
            }

            if($service->serviceDocuments->count() > 0) {
                foreach($service->serviceDocuments as $document) $document->delete();
            }
        });

        static::created(function (UserService $service) {
            // $service->ratings = $service->rating();
            // $service->update();
        });
    }
}
