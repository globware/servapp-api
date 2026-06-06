<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Exceptions\AppException;

use App\Http\Requests\Provider\SendChatMessage;
use App\Http\Requests\CompleteService;
use App\Http\Requests\TreatRequestCompleted;

use App\Http\Resources\ServiceRequestResource;
use App\Http\Resources\ChatResource;

use App\Services\ServiceRequestService;
use App\Services\ChatService;

use App\Models\UserService;
use App\Models\VwUserServiceRequestCount;
use App\Models\VwUserServiceRequestCountByUser;

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

    public function accept($requestId)
    {
        try{
            $this->requestService->accept($requestId);

            return Utilities::okay("Request Accepted Successfully");
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function getRequests()
    {
        $requests = $this->requestService->getProviderRequests(Auth::user()->id, ['userService', 'user']);

        return Utilities::ok(ServiceRequestResource::collection($requests));
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

    public function completed(CompleteService $request, int $requestId)
    {
        try{
            $data = $request->validated();
            $data['completedBy'] = 'provider';

            $request = $this->requestService->complete($requestId, $data, Auth::user()->id);

            return Utilities::okay("Successful");
        } catch(AppException $e) {
            throw $e;
        } catch(\Exception $e) {
            return Utilities::error($e, "An Error Occurred while attempting to perform this operation");
        }
    }

    public function treatCompleted(TreatRequestCompleted $request, int $requestId)
    {
        try{
            $this->requestService->treatCompleted($requestId, $request->validated("approved"), Auth::user()->id);

            return Utilities::okay("Successful");
        } catch(AppException $e) {
            throw $e;
        } catch(\Exception $e) {
            return Utilities::error($e, "An Error Occurred while attempting to perform this operation");
        }
    }

    public function stats($serviceId=null)
    {
        $providerRequestCount = VwUserServiceRequestCountByUser::where("owner_user_id", Auth::user()->id)->first();
        $userStats = [
            "services" => $providerRequestCount->total_services,
            "requests" => $providerRequestCount->total_requests,
            "pendingRequests" => $providerRequestCount->pending_count,
            "engagedRequests" => $providerRequestCount->engaged_count,
            "completedRequests" => $providerRequestCount->completed_count,
            "cancelledRequests" => $providerRequestCount->cancelled_count,
        ];

        $stats = ["userStats" => $userStats];
        if($serviceId) {
            $serviceRequestCount = VwUserServiceRequestCount::where("user_service_id", $serviceId)->first();
            $serviceStats = [
                "requests" => $serviceRequestCount->total_requests,
                "pendingRequests" => $serviceRequestCount->pending_count,
                "engagedRequests" => $serviceRequestCount->engaged_count,
                "completedRequests" => $serviceRequestCount->completed_count,
                "cancelledRequests" => $serviceRequestCount->cancelled_count,
            ];
            $stats['serviceStats'] = $serviceStats;
        }

        return Utilities::ok($stats);
    }
}
