<?php

namespace App\Services;

use App\Exceptions\AppException;

use App\Models\Chat;
use App\Models\User;
use App\Models\UserService;

use App\Services\ServiceRequestService;

class ChatService
{
    public $count = [];
    public $seen = null;
    public $receiverId = null;
    public $receiverType = null;
    public $senderId = null;
    public $senderType = null;

    public function getMessages($requestId, $with=[])
    {
        $query = Chat::with($with)->withCount($this->count)->where("user_service_request_id", $requestId);
        if($this->seen != null) $query->where("seen", $this->seen);
        if($this->receiverId && $this->receiverType) $query->where("receiver_type", $this->receiverType)->where("receiver_id", $this->receiverId);
        if($this->senderId && $this->senderType) $query->where("sender_type", $this->senderType)->where("sender_id", $this->senderId);

        return $query->orderBy("created_at", "DESC")->get();
    }

    public function sendMessage($data)
    {
        try{
            $chat = new Chat;

            $chat->user_service_request_id = $data['requestId'];

            $chat->sender_id = $data['entityId'];
            $chat->sender_type = $data['entityType'];
            $chat->receiver_id = ($data['entityType'] == User::$type) ? $data['request']->user_service_id : $data['request']->user_id;
            $chat->receiver_type = ($data['entityType'] == User::$type) ? UserService::$type : User::$type;
            $chat->message = $data['message'];
            
            $chat->save();

            return $chat;
        }catch(\Exception $e){
            // $errorCode = AppException::getDefaultErrorCode(402);
            throw new AppException(500, null, $e);
        }
    }

    public function markAsSeen($requestId, $entityId, $entityType)
    {
        try{
            $this->receiverId = $entityId;
            $this->receiverType = $entityType;
            $this->seen = false;
            $chats = $this->getMessages($requestId);
            if($chats->count() > 0) {
                foreach($chats as $chat) {
                    $chat->seen = true;
                    $chat->update();
                }
            }
             // reset receiver
             $this->receiverId = $this->receiverType = $this->seen = null;
        } catch(\Exception $e){
            throw new AppException(500, null, $e);
        }
    }
}