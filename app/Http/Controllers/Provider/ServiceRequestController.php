<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Exceptions\AppException;

use App\Http\Requests\Provider\SendChatMessage;

use App\Http\Resources\ServiceRequestResource;
use App\Http\Resources\ChatResource;

use App\Services\ServiceRequestService;
use App\Services\ChatService;

use App\Models\UserService;

use App\Utilities;

class ServiceRequestController extends Controller
{
    protected $requestService;
    protected $chatService;

    public function __construct(ServiceRequestService $requestService, ChatService $chatService)
    {
        $this->requestService = $requestService;
        $this->chatService = $chatService;
    }

    public function getRequest($requestId)
    {
        try{
            $serviceRequest = $this->requestService->getRequest($requestId, ['user']);
            if(!$serviceRequest) return Utilities::error402("Service Request does not exist");

            if($serviceRequest->userService->user_id != Auth::user()->id) return Utilities::error402("You are not authorized to view this request");

            return Utilities::ok(new ServiceRequestResource($serviceRequest, UserService::$type));
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function sendMessage(SendChatMessage $request)
    {
        try{
            $data = $request->validated();
            $serviceRequest = $this->requestService->getRequest($data['requestId']);
            if(!$serviceRequest) return Utilities::error402("Service Request not found");

            $data['request'] = $serviceRequest;
            $data['entityId'] = $serviceRequest->user_service_id;
            $data['entityType'] = UserService::$type;
            
            $this->chatService->sendMessage($data);

            return Utilities::okay("Message sent");
            
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function getRequestChats($requestId)
    {
        try{
            $serviceRequest = $this->requestService->getRequest($requestId, ['user']);
            if(!$serviceRequest) return Utilities::error402("Service Request does not exist");

            if($serviceRequest->userService->user_id != Auth::user()->id) return Utilities::error402("You are not authorized to view this request");

            // mark all the chat sent to this user Service as seen by the user Service
            $this->chatService->markAsSeen($requestId, $serviceRequest->userService->id, UserService::$type);
            
            $chats = $this->chatService->getMessages($requestId);

            return Utilities::ok(ChatResource::collection($chats));
        } catch(\Exception $e) {
            throw $e;
        }
    }
}
