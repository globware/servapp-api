<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(Admin::class, "resolved_by", "id");
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (Ticket $ticket) {
            if($ticket->messages->count() > 0) {
                foreach($ticket->messages as $message) $message->delete();
            }
        });
    }
}
