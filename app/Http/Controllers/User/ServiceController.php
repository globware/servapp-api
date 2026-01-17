<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exceptions\AppException;

use App\Http\Requests\SendMessage;
use App\Http\Requests\User\ReviewService as ReviewServiceRequest;
use App\Http\Requests\User\RateService;
use App\Http\Requests\User\Complain;

use App\Http\Resources\ServiceResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserServiceResource;

use App\Services\ServiceService;
use App\Services\UserServiceService;
use App\Services\MessageService;
use App\Services\ServiceRequestService;
use App\Services\ReviewService;
use App\Services\RatingService;
use App\Services\ComplaintService;

use App\Models\UserService;
use App\Models\User;

use App\Utilities;

class ServiceController extends Controller
{
    protected $serviceService;
    protected $userServiceService;
    protected $messageService;
    protected $requestService;
    protected $reviewService;
    protected $ratingService;
    protected $complaintService;

    public function __construct(ServiceService $serviceService, UserServiceService $userServiceService, MessageService $messageService, 
                                    ServiceRequestService $requestService, ReviewService $reviewService, RatingService $ratingService, 
                                    ComplaintService $complaintService
                                )
    {
        $this->serviceService = $serviceService;
        $this->userServiceService = $userServiceService;
        $this->messageService = $messageService;
        $this->requestService = $requestService;
        $this->reviewService = $reviewService;
        $this->ratingService = $ratingService;
        $this->complaintService = $complaintService;
    }

    public function getServices(Request $request)
    {
        $this->serviceService->limit = 5;
        $this->serviceService->approved = true;
        $services = collect([]);
        $services = $this->serviceService->getServices();

        return Utilities::ok(ServiceResource::collection($services));
        
    }

    public function getServicesByLocation(Request $request)
    {
        $long = $request->query("long");
        $lat = $request->query("lat");

        $this->serviceService->limit = 5;
        $this->serviceService->approved = true;
        $services = collect([]);
        if($long && $lat) {
            $services = $this->serviceService->getByGps($long, $lat);
        }

        if($services->count() == 0) {
            $services = $this->serviceService->getByLocation(Auth::user()->location_id);

            if($services->count() == 0) {
                $services = $this->serviceService->getServices();
            }
        }

        return Utilities::ok(ServiceResource::collection($services));
        
    }

    public function getService(Request $request, $serviceId)
    {
        $service = $this->serviceService->getService($serviceId, ['userServices']);
        if(!$service) return Utilities::error402("Service not found");

        return Utilities::ok(new ServiceResource($service));
    }

    public function getUserService(Request $request, $serviceId)
    {
        $this->userServiceService->count = ['requests'];
        $userService = $this->userServiceService->getService($serviceId, ['media', 'tags', 'reviews']);
        if(!$userService) return Utilities::error402("Service not found");

        $requests = $this->requestService->getUserRequests(Auth::user()->id, ['service']);

        return Utilities::ok(new UserServiceResource($userService, $requests));
    }

    public function sendMessage(SendMessage $request)
    {
        try{
            $data = $request->validated();

            $userService = $this->userServiceService->getService($data['receiverId']);
            if(!$userService) return Utilities::error402("This User Service does not exist");

            if($userService->user_id == Auth::user()->id) return Utilities::error402("You cannot message yourself");

            $data['receiverType'] = UserService::$type;
            $data['senderId'] = Auth::user()->id;
            $data['senderType'] = User::$type;

            $message = $this->messageService->send($data);

            return Utilities::ok(new MessageResource($message));
        } catch (AppException $e) {
            throw $e;
        }
    }

    public function readMessage($serviceId)
    {
        try{
            $userService = $this->userServiceService->getService($serviceId);
            if(!$userService) return Utilities::error402("This User Service does not exist");

            $this->messageService->read = false;
            $this->messageService->markAsRead($userService, UserService::class);

            return Utilities::okay("Messages marked as read");
        } catch (AppException $e) {
            throw $e;
        }
    }

    public function review(ReviewServiceRequest $request)
    {
        try{
            $data = $request->validated();
            $data['userId'] = Auth::user()->id;
            $data['targetId'] = $data['serviceId'];
            $data['targetType'] = UserService::$type;

            $this->reviewService->save($data);

            return Utilities::okay("Review sent");
        } catch (AppException $e) {
            throw $e;
        }
    }

    /*
        Rate a Service
    **/
    public function rate(RateService $request)
    {
        try{
            $data = $request->validated();

            $request = $this->requestService->getRequest($data['requestId']);
            if(!$request) return Utilities::error402("User Request is not found");

            if($request->userService->user_id == Auth::user()->id) return Utilities::error402("You cannot rate yourself!");

            if($request->user_id != Auth::user()->id) return Utilities::error402("You are not authorized to rate this");

            $data['userId'] = Auth::user()->id;
            $data['targetId'] = $request->user_service_id;
            $data['targetType'] = UserService::$type;

            $this->ratingService->save($data);

            return Utilities::okay("Rating sent");
        } catch (AppException $e) {
            throw $e;
        }
    }

    public function complain(Complain $request)
    {
        try{
            $data = $request->validated();
            $data['userId'] = Auth::user()->id;

            $service = $this->userServiceService->getService($data['serviceId']);
            if(!$service) return Utilities::error402("The target User Service does not exist");

            $data['targetId'] = $data['serviceId'];
            $data['targetType'] = UserService::$type;

            $this->complaintService->save($data);

            return Utilities::okay("Complaint has been received successfully");
            
        } catch (AppException $e) {
            throw $e;
        }
    }
}
