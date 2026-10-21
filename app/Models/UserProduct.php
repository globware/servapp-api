<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class UserProduct extends Model
{
    use Searchable;

    public static $type = "App\Models\UserProduct";

    protected $fillable = [
        'name',
        'description',
        'price',
        'address',
        'latitude',
        'longitude',
        'product_id',
        'user_id',
        'ratings',
        'country_id',
        'state_id',
        'location_id',
        'verified',
        'suspended',
        'active',
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name, // Assuming it has name, will verify schema later
            'description' => $this->description,
            'address' => $this->address,
            'is_unclaimed' => false,
            'active' => $this->active ?? true,
            '_geo' => [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ],
        ];
    }

    public function getType()
    {
        return Self::$type;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function reviews()
    {
        return $this->hasMany(UserProductReview::class);
    }

    public function patronizers()
    {
        return $this->morphMany(Patronizer::class, 'target');
    }

    public function userRatings()
    {
        return $this->morphMany(Rating::class, 'target');
    }

    public function productMedia()
    {
        return $this->hasMany(UserProductMedia::class);
    }

    public function media()
    {
        return $this->belongsToMany(File::class, 'user_product_media')
                    ->withTimestamps();
    }

    // public function messages()
    // {
    //     return $this->hasMany(UserProductMessage);
    // }
    public function inbox()
    {
        return $this->morphMany(Message::class, "receiver");
    }

    public function outbox()
    {
        return $this->morphMany(Message::class, "sender");
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

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (UserProduct $product) {
            if($product->userRatings->count() > 0) {
                foreach($product->userRatings as $rating) $rating->delete();
            }

            if($product->reviews->count() > 0) {
                foreach($product->reviews as $review) $review->delete();
            }

            if($product->patronizers->count() > 0) {
                foreach($product->patronizers as $patronizer) $patronizer->delete();
            }

            if($product->productMedia->count() > 0) {
                foreach($product->productMedia as $media) $media->delete();
            }

            if($product->messages->count() > 0) {
                foreach($product->messages as $message) $message->delete();
            }
        });

        static::created(function (UserProduct $product) {
            $product->ratings = $product->rating();
            $product->updateQuietly();
        });
    }
}
