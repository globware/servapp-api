<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public static $type = "App\Models\User";

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute()
    {
        $name = '';
        if($this->firstname && !empty($this->firstname)) $name .= $this->firstname.' ';
        if($this->surname && !empty($this->surname)) $name .= $this->surname.' ';
        return $name;
    }

    public function getType()
    {
        return Self::$type;
    }

    public function photo()
    {
        return $this->belongsTo(File::class, "photo_id", "id");
    }

    public function identification()
    {
        return $this->belongsTo(Identification::class);
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, "referred_by", "id");
    }

    public function inbox()
    {
        return $this->morphMany(Message::class, "receiver");
    }

    public function outbox()
    {
        return $this->morphMany(Message::class, "sender");
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'user_services')
                    ->withPivot(['phone_numbers', 'email', 'verified', 'suspended', 'longitude', 'latitude'])
                    ->withTimestamps();
    }

    public function userServices()
    {
        return $this->hasMany(UserService::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, "complainer_id", "id");
    }

    public function complaintTargets()
    {
        return $this->hasMany(Complaint::class, "target_id", "id");
    }

    public function serviceRatings()
    {
        return $this->hasMany(ServiceRating::class);
    }

    public function serviceReviews()
    {
        return $this->hasMany(ServiceReview::class);
    }

    public function servicePatronizers()
    {
        return $this->hasMany(ServicePatronizer::class);
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (User $user) {
            if($user->inbox->count() > 0) {
                foreach($user->inbox as $message) {
                    $message->receiver_id = null;
                    $message->is_orphaned = true;
                    $message->update();
                }
            }

            if($user->outbox->count() > 0) {
                foreach($user->outbox as $message) {
                    $message->sender_id = null;
                    $message->is_orphaned = true;
                    $message->update();
                }
            }

            if($user->userServices->count() > 0) {
                foreach($user->userServices as $userService) {
                    $userService->delete();
                }
            }

            if($user->tickets->count() > 0) {
                foreach($user->tickets as $ticket) $ticket->delete();
            }

            if($user->complaints->count() > 0) {
                foreach($user->complaints as $complaint) $complaint->delete();
            }

            if($user->complaintTargets->count() > 0) {
                foreach($user->complaintTargets as $complaint) {
                    $complaint->target_id = null;
                    $complaint->is_orphaned = true;
                    $complaint->update();
                }
            }

            if($user->serviceRatings->count() > 0) {
                foreach($user->serviceRatings as $rating) $rating->delete();
            }

            if($user->serviceReviews->count() > 0) {
                foreach($user->serviceReviews as $review) $review->delete();
            }

            if($user->servicePatronizers->count() > 0) {
                foreach($user->servicePatronizers as $patronizer) $patronizer->delete();
            }

        });
    }
    
}
