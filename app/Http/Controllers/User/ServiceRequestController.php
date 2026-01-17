<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exceptions\AppException;

use App\Http\Requests\User\RequestService;
use App\Http\Requests\User\SendChatMessage;

use App\Http\Resources\ServiceRequestResource;
use App\Http\Resources\ChatResource;

use App\Services\ServiceRequestService;
use App\Services\ChatService;

use App\Models\User;

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

    public function requestService(RequestService $request)
    {
        try{
            $data = $request->validated();
            $data['userId'] = Auth::user()->id;
            $serviceRequest = $this->requestService->save($data);

            return Utilities::ok(new ServiceRequestResource($serviceRequest));
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function getRequest($requestId)
    {
        try{
            $serviceRequest = $this->requestService->getRequest($requestId, ['user']);
            if(!$serviceRequest) return Utilities::error402("Service Request does not exist");

            if($serviceRequest->user_id != Auth::user()->id) return Utilities::error402("You are not authorized to view this request");

            return Utilities::ok(new ServiceRequestResource($serviceRequest, User::$type));
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
            $data['entityId'] = Auth::user()->id;
            $data['entityType'] = User::$type;
            
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

            if($serviceRequest->user_id != Auth::user()->id) return Utilities::error402("You are not authorized to view this chats");

            // dd($this->chatService->receiverId);
            // mark all the chat sent to this user as seen by the user
            $this->chatService->markAsSeen($requestId, Auth::user()->id, User::$type);
            
            $chats = $this->chatService->getMessages($requestId);

            return Utilities::ok(ChatResource::collection($chats));
        } catch(\Exception $e) {
            throw $e;
        }
    }

    // public function markAsSeen($requestId)
}
