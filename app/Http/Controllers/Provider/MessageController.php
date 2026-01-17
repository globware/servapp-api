<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\SaveMedia;
use App\Http\Requests\SendMessage;

use App\Http\Resources\UserServiceResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ConversationResource;

use App\Services\UserServiceService;
use App\Services\MessageService;
use App\Services\UserService;

use App\Models\UserService as UserServiceModel;
use App\Models\User;

use App\Utilities;

class MessageController extends Controller
{
    protected $service;
    protected $messageService;
    protected $userService;

    public function __construct(
        UserServiceService $service, MessageService $messageService, UserService $userService
    )
    {
        $this->service = $service;
        $this->messageService = $messageService;
        $this->userService = $userService;
    }

    public function conversations(Request $request, $userServiceId)
    {
        try {
            $userService = $this->service->getService($userServiceId);
            if(!$userService) return Utilities::error402("This User Service does not exist");

            $conversations = $this->messageService->getConversations($userService, UserServiceModel::class);

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

    public function sendMessage(SendMessage $request, $userServiceId)
    {
        try{
            $data = $request->validated();

            $userService = $this->service->getService($userServiceId);
            if(!$userService) return Utilities::error402("This User Service does not exist");

            $user = $this->userService->getUser($data['receiverId']);
            if(!$user) return Utilities::error402("This User does not exist");

            if($user->id == Auth::user()->id) return Utilities::error402("You cannot message yourself");

            $data['receiverType'] = User::$type;
            $data['senderId'] = $userService->id;
            $data['senderType'] = UserServiceModel::$type;

            $message = $this->messageService->send($data);

            return Utilities::ok(new MessageResource($message));
        } catch (AppException $e) {
            throw $e;
        }
    }

    public function readMessage($userId)
    {
        try{
            $user = $this->userService->getUser($userId);
            if(!$user) return Utilities::error402("User not found");

            $this->messageService->read = false;
            $this->messageService->markAsRead($user, User::class);

            return Utilities::okay("Messages marked as read");
        } catch (AppException $e) {
            throw $e;
        }
    }
}
