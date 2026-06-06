<?php 

namespace App\Services;

use Carbon\Carbon;
use App\Exceptions\AppException;

use App\Models\UserServiceRequest;
use App\Models\Chat;

use App\Services\UserServiceService;
use App\Services\FcmService;

use App\Enums\ServiceRequestStatus;

class ServiceRequestService 
{
    public $count = [];
    public $serviceId = null;
    public $status = null;

    public function save(array $data)
    {
        $service = new UserServiceService;
        $userService = $service->getService($data['serviceId']);

        if($userService->user_id == $data['userId']) throw new AppException(402, "You cannot request for your own service");

        $alreadyRequestedToday = UserServiceRequest::where('user_service_id', $data['serviceId'])
        ->where('user_id', $data['userId'])
        ->whereDate('created_at', Carbon::today())
        ->exists();

        // if ($alreadyRequestedToday) {
        //     throw new AppException(402, "You have already requested this service today");
        // }

        $request = new UserServiceRequest;
        $request->user_service_id = $data['serviceId'];
        $request->user_id = $data['userId'];
        $request->message = $data['message'];
        $request->save();

        $topic = 'user_'.$userService->user_id;
        $meta = [
            "request" => $request
        ];
        $data = [
                "topic" => $topic,
                "message" => $data['message'],
                "type" => "service_request",
                "title" => "Service Request",
                "meta" => $meta
        ];
        app(FcmService::class)->publish($data);

        return $request;
    }

    public function accept(int $requestId)
    {
        $request = $this->getRequest($requestId);
        if(!$request) throw new AppException(402, "Service Request not found");

        $request->status = ServiceRequestStatus::ENGAGED->value;
        $request->update();

        $topic = 'user_'.$request->user_id;
        $meta = [
            "request" => $request
        ];
        $data = [
                "topic" => $topic,
                "message" => '',
                "type" => "request_accepted",
                "title" => "Service Request Accepted",
                "meta" => $meta
        ];
        app(FcmService::class)->publish($data);

        return $request;
    }

    public function cancel($requestId)
    {
        $request = $this->getRequest($requestId);
        if(!$request) throw new AppException(402, "Service Request not found");

        $request->status = ServiceRequestStatus::CANCELLED->value;
        $request->update();

        $topic = 'user_'.$request->userService->user_id;
        $meta = [
            "request" => $request
        ];
        $data = [
                "topic" => $topic,
                "message" => '',
                "type" => "request_cancelled",
                "title" => "Service Request Cancelled",
                "meta" => $meta
        ];
        app(FcmService::class)->publish($data);

        return $request;
    }

    public function complete(int $requestId, array $data, int $userId)
    {
        $request = $this->getRequest($requestId);
        if(!$request) throw new AppException(402, "Service Request not found");

        if($data['completedBy'] == 'user') {
            if($request->user_id != $userId) throw new AppException(402, "You are not eligible to perform this operation");
        }else{
            if($request->userService->user_id != $userId) throw new AppException(402, "You are not eligible to perform this operation");
        }

        $request->completed_by = $data['completedBy'];
        $request->service_rendered = $data['rendered'];
        if(isset($data['reason'])) $request->unrendered_reason = $data['reason'];
        $request->status = ServiceRequestStatus::COMPLETED->value;
        $request->save();


        $receiverUserId = ($data['completedBy'] == 'user') ? $request->user_id : $request->userService->user_id; 
        $topic = 'user_'.$receiverUserId;
        $meta = [
            "request" => $request
        ];
        $data = [
                "topic" => $topic,
                "message" => '',
                "type" => "request_completed",
                "title" => "Service Request Completed",
                "meta" => $meta
        ];
        app(FcmService::class)->publish($data);

        return $request;
    }

    public function treatCompleted(int $requestId, bool $approved, int $userId)
    {
        $request = $this->getRequest($requestId);
        if(!$request) throw new AppException(402, "Service Request not found");

        if($request->completed_by == 'user') {
            if($request->userService->user_id != $userId) throw new AppException(402, "You are not eligible to perform this operation");
        }else{
            if($request->user_id != $userId) throw new AppException(402, "You are not eligible to perform this operation");
        }

        $request->completed_approved = $approved;
        $request->save();

        return $request;
    }

    public function getServiceRequests(int $serviceId)
    {
        return UserServiceRequest::with(['user', 'chats'])->where("user_service_id", $serviceId)->where("seen", false)->get();
    }

    public function getUserRequests(int $userId, $with=[])
    {
        if(!in_array("chats", $with)) $with[] = "chats";

        $query = UserServiceRequest::with($with)->withCount($this->count)->where("user_id", $userId);
        if($this->serviceId) $query->where("user_service_id", $this->serviceId);

        return $query->orderBy("created_at", "DESC")->get();
    }

    public function getProviderRequests($userId, $with=[])
    {
        if(!in_array("chats", $with)) $with[] = "chats";

        $query = UserServiceRequest::with($with)->withCount($this->count)->whereHas("userService", function($serviceQuery) use($userId) { 
            $serviceQuery->where("user_id", $userId);
        });

        if($this->serviceId) $query->where("user_service_id", $this->serviceId);

        return $query->orderBy("created_at", "DESC")->get();
    }

    public function getRequests($with=[])
    {
        return UserServiceRequest::with($with)->when($this->status, function($query) {
            $query->where("status", $this->status);
        })->get();
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