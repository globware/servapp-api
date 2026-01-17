<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exceptions\AppException;

use App\Http\Resources\ConversationResource;

use App\Services\MessageService;

use App\Models\User;

use App\Utilities;

class MessageController extends Controller
{
    public $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function conversations(Request $request)
    {
        try {
            $user = Auth::user();
            $conversations = $this->messageService->getConversations($user, User::class);

            // Map conversations to resources
            $conversationResources = array_map(function($conversation) {
                return new ConversationResource($conversation);
            }, $conversations);

            return Utilities::ok([
                'conversations' => $conversationResources
            ]);
        } catch (AppException $e) {
            throw $e;
        }
    }
}
