<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exceptions\AppException;

use App\Http\Requests\Provider\Complain;

use App\Services\ComplaintService;
use App\Services\UserServiceService;

use App\Models\UserService;
use App\Models\User;

use App\Utilities;

class ComplaintController extends Controller
{
    protected $complaintService;
    protected $service;

    public function __construct(ComplaintService $complaintService, UserServiceService $service)
    {
        $this->complaintService = $complaintService;
        $this->service = $service;
    }

    public function save(Complain $request)
    {
        try{
            $data = $request->validated();

            if(Auth::user()->id == $data['userId']) return Utilities::error402("You cannot complain about yourself!");

            $data['targetId'] = $data['userId'];
            $data['targetType'] = User::$type;

            $data['userId'] = Auth::user()->id;

            $service = $this->service->getService($data['serviceId']);
            if(!$service) return Utilities::error402("This User Service does not exist");

            if($service->user_id != Auth::user()->id) return Utilities::error402("Wrong service");

            $data['referenceId'] = $data['serviceId'];
            $data['referenceType'] = UserService::$type;

            $this->complaintService->save($data);

            return Utilities::okay("Complaint has been received successfully");
            
        } catch (AppException $e) {
            throw $e;
        }
    }
}
