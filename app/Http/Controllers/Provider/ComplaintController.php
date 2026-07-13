<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exceptions\AppException;

use App\Http\Requests\Provider\Complain;

use App\Services\ComplaintService;
use App\Services\UserServiceService;
use App\Services\ServiceRequestService;

use App\Models\UserService;
use App\Models\User;

use App\Utilities;

class ComplaintController extends Controller
{

    public function __construct(
        protected ComplaintService $complaintService,
        protected UserServiceService $service,
        protected ServiceRequestService $requestService
    ) {
        $this->complaintService = $complaintService;
        $this->service = $service;
    }

    public function save(Complain $request)
    {
        try {
            $data = $request->validated();

            $serviceRequest = $this->requestService->getRequest($data['requestId']);
            if (!$serviceRequest) return Utilities::error402("The service request does not exist");

            if ($serviceRequest->userService->user_id != Auth::user()->id) {
                return Utilities::error402("You are not authorized to complain about this request");
            }

            $complaintData = [
                'userId' => Auth::user()->id,
                'targetId' => $serviceRequest->id,
                'targetType' => \App\Models\UserServiceRequest::$type,
                'referenceId' => $serviceRequest->user_service_id,
                'referenceType' => \App\Models\UserService::$type,
                'title' => $data['title'],
                'content' => $data['content']
            ];

            $this->complaintService->save($complaintData);

            return Utilities::okay("Complaint has been received successfully");
        } catch (AppException $e) {
            throw $e;
        }
    }
}
