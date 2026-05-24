<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exceptions\AppException;

use App\Http\Requests\User\RequestService;
use App\Http\Requests\User\SendChatMessage;
use App\Http\Requests\User\LeaveFeedback;
use App\Http\Requests\CompleteService;
use App\Http\Requests\TreatRequestCompleted;

use App\Http\Resources\ServiceRequestResource;
use App\Http\Resources\ChatResource;

use App\Services\ServiceRequestService;
use App\Services\ChatService;
use App\Services\FeedbackService;

use App\Models\User;
use App\Models\UserService;
use App\Models\UserServiceRequest;
use App\Utilities;

class ServiceRequestController extends Controller
{

    public function __construct(protected ServiceRequestService $requestService, protected ChatService $chatService, protected FeedbackService $feedbackService)
    {
    }

    public function requestService(RequestService $request)
    {
        try{
            $data = $request->validated();
            $data['userId'] = Auth::user()->id;
            $serviceRequest = $this->requestService->save($data);

            return Utilities::ok(new ServiceRequestResource($serviceRequest));
        } catch(AppException $e) {
            throw $e;
        } catch(\Exception $e) {
            return Utilities::error($e, "An Error Occurred while attempting to perform this operation");
        }
    }

    public function cancel($requestId)
    {
        try{
            $request = $this->requestService->cancel($requestId);

            return Utilities::ok(new ServiceRequestResource($request));
        } catch(AppException $e) {
            throw $e;
        } catch(\Exception $e) {
            return Utilities::error($e, "An Error Occurred while attempting to perform this operation");
        }
    }

    public function getRequest($requestId)
    {
        try{
            $serviceRequest = $this->requestService->getRequest($requestId, ['user']);
            if(!$serviceRequest) return Utilities::error402("Service Request does not exist");

            if($serviceRequest->user_id != Auth::user()->id) return Utilities::error402("You are not authorized to view this request");

            return Utilities::ok(new ServiceRequestResource($serviceRequest, User::$type));
        } catch(AppException $e) {
            throw $e;
        } catch(\Exception $e) {
            return Utilities::error($e, "An Error Occurred while attempting to perform this operation");
        }
    }

    public function getRequests()
    {
        $requests = $this->requestService->getUserRequests(Auth::user()->id, ['userService.media', 'userService.service']);

        return Utilities::ok(ServiceRequestResource::collection($requests));
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
        } catch(AppException $e) {
            throw $e;
        } catch(\Exception $e) {
            return Utilities::error($e, "An Error Occurred while attempting to perform this operation");
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
        } catch(AppException $e) {
            throw $e;
        } catch(\Exception $e) {
            return Utilities::error($e, "An Error Occurred while attempting to perform this operation");
        }
    }

    public function completed(CompleteService $request, int $requestId)
    {
        try{
            $data = $request->validated();
            $data['completedBy'] = 'user';

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

    public function feedback(LeaveFeedback $request, int $id)
    {
        try{
            $userRequest = $this->requestService->getRequest($id);
            if(!$userRequest) return Utilities::error402("Request not found");

            $data = $request->validated();
            $data['userId'] = Auth::user()->id;
            $data['targetType'] = UserServiceRequest::$type;
            $data['targetId'] = $id;

            $this->feedbackService->save($data);

            return Utilities::okay("successful");
        } catch(\Exception $e){
            return Utilities::error($e, "An Error Occurred while attempting to leave feedback");
        }
    }

    // public function markAsSeen($requestId)
}
