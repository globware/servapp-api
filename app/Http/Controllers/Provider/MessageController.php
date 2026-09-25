<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat;
use App\Http\Resources\ChatResource;
use App\Utilities;

class MessageController extends Controller
{
    /**
     * @return array{status: boolean, message: string, data: array<\App\Http\Resources\ChatResource>}
     */
    public function conversations()
    {
        $userId = Auth::id();
        
        $chats = Chat::with(['sender', 'receiver'])
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($item) {
                return $item->requestable_type . '-' . $item->requestable_id;
            })
            ->values();

        return Utilities::ok(ChatResource::collection($chats));
    }
}
