<?php 

namespace App\Services;

use Carbon\Carbon;
use App\Exceptions\AppException;

use App\Models\UserServiceRequest;
use App\Models\Chat;

use App\Services\UserServiceService;

class ServiceRequestService 
{
    public $count = [];
    public $serviceId = null;

    public function save($data)
    {
        $service = new UserServiceService;
        $userService = $service->getService($data['serviceId']);

        if($userService->user_id == $data['userId']) throw new AppException(402, "You cannot request for your own service");

        $alreadyRequestedToday = UserServiceRequest::where('user_service_id', $data['serviceId'])
        ->where('user_id', $data['userId'])
        ->whereDate('created_at', Carbon::today())
        ->exists();

        if ($alreadyRequestedToday) {
            throw new AppException(402, "You have already requested this service today");
        }

        $request = new UserServiceRequest;
        $request->user_service_id = $data['serviceId'];
        $request->user_id = $data['userId'];
        $request->message = $data['message'];
        $request->save();

        return $request;
    }

    public function getServiceRequests($serviceId)
    {
        return UserServiceRequest::with(['user', 'chats'])->where("user_service_id", $serviceId)->where("seen", false)->get();
    }

    public function getUserRequests($userId, $with=[])
    {
        if(!in_array("chats", $with)) $with[] = "chats";

        $query = UserServiceRequest::with($with)->withCount($this->count)->where("user_id", $userId);
        if($this->serviceId) $query->where("user_service_id", $this->serviceId);

        return $query->orderBy("created_at", "DESC")->get();
    }

    public function getRequest($requestId, $with=[])
    {
        return UserServiceRequest::with($with)->where("id", $requestId)->first();
    }

    public function getRequestChats($requestId)
    {
        return Chat::where("user_service_request_id", $requestId)->orderBy("created_at", "DESC")->get();
    }

    public function sendMessage($data, $requestId)
    {
        $request = $this->getRequest($requestId);
        if(!$request) throw new AppException(402, "Service Request not found");

        $chat = new Chat;
        $chat->user_service_request_id = $requestId;
        $chat->sender_id = $data['senderId'];
        $chat->sender_type = $data['senderType'];
        $chat->receiver_id = $data['receiverId'];
        $chat->receiver_type = $data['receiverType'];
        $chat->message = $data['message'];
        $chat->save();

        return $chat;
    }

    public function markAsSeen($requestId, $entityId, $entityType)
    {
        $chats = Chat::where("user_service_request_id", $requestId)->where("receiver_type", $entityType)
                    ->where("receiver_id", $entityId)->where("seen", false)->get();
        if($chats->count() > 0) {
            foreach($chats as $chat) {
                $chat->seen = true;
                $chat->update();
            }
        }
    }
}