<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Exceptions\AppException;

use App\Http\Requests\Provider\AddService;
use App\Http\Requests\Provider\UpdateService;
use App\Http\Requests\Provider\AddServiceMedia;
use App\Http\Requests\Provider\AddServiceTags;
use App\Http\Requests\SaveMedia;
use App\Http\Requests\SendMessage;

use App\Http\Resources\UserServiceResource;
use App\Http\Resources\MessageResource;

use App\Services\UserServiceService;
use App\Services\FileService;
use App\Services\UserServiceUpdateLogService;
use App\Services\UserService;

use App\Models\UserService as UserServiceModel;
use App\Models\User;

use App\Utilities;

class ServiceController extends Controller
{
    protected $service;
    protected $fileService;
    protected $updateLogService;
    protected $userService;

    public function __construct(
        UserServiceService $service, FileService $fileService, UserServiceUpdateLogService $updateLogService,
        UserService $userService
    )
    {
        $this->service = $service;
        $this->fileService = $fileService;
        $this->updateLogService = $updateLogService;
        $this->userService = $userService;
    }

    public function service($serviceId)
    {
        $this->service->count = ['requests'];
        $service = $this->service->getService($serviceId, ['media', 'tags', 'requests']);

        if(!$service) return Utilities::error402("Service not found");

        if($service->user_id != Auth::user()->id) return Utilities::error402("You are not authorized to get this Service");

        return Utilities::ok(new UserServiceResource($service));
    }

    public function services(Request $request)
    {
        $this->service->userId = Auth::user()->id;
        $this->service->count = ['requests'];
        $services = $this->service->getServices(['media', 'tags', 'reviews']);
        
        return Utilities::ok(UserServiceResource::collection($services));
    }

    public function save(AddService $request)
    {
        try{
            $data = $request->validated();

            if(isset($data['allDay']) && !$data['allDay']) unset($data['allDay']);

            $data['userId'] = Auth::user()->id;

            $userService = $this->service->save($data);
            $userService->load(["media", "tags"]);

            return Utilities::ok(new UserServiceResource($userService));
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function saveMedia(SaveMedia $request)
    {
        try{
            $fileArr = [];
            $fileIds = [];
            foreach($request->validated("media") as $file) {
                $fileArr[] = $this->fileService->save($file, 'services');
            }
            if(!empty($fileArr)) {
                foreach($fileArr as $file) $fileIds[] = $file->id;
            }

            return Utilities::ok($fileIds);
        } catch(\Exception $e) {
            return Utilities::error($e, "An Error Occurred while attempting to Save Service Media");
        }
    }

    public function update(UpdateService $request, $serviceId)
    {
        DB::beginTransaction();
        try{
            $data = $request->validated();

            $service = $this->service->getService($serviceId);
            if(!$service) return Utilities::error402("User Service not found");

            $res = $this->service->update($data, $service);
            $service = $res['service'];
            if(!empty($res['updated'])) {
                $this->updateLogService->save(["serviceId" => $service->id, "updates" => $res['updated']]);
            }

            DB::commit();

            $service = $this->service->getService($service->id, ['media, tags']);

            return Utilities::ok(new UserServiceResource($service));

        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function addServiceMedia(AddServiceMedia $request, $serviceId)
    {
        try{
            $service = $this->service->getService($serviceId);
            if(!$service) return Utilities::error402("User Service not found");

            $this->service->addMedia($service, $request->validated("mediaIds"));
            $service = $this->service->getService($serviceId, ['media', 'tags']);

            return Utilities::ok(new UserServiceResource($service));
            
        } catch(\Exception $e) {
            return Utilities::error($e, "An Error Occurred while attempting to Add Service Media");
        }
    }

    public function addServiceTags(AddServiceTags $request, $serviceId)
    {
        try{
            $service = $this->service->getService($serviceId);
            if(!$service) return Utilities::error402("User Service not found");

            $this->service->addServiceTags($service, $request->validated("tags"));
            $service = $this->service->getService($serviceId, ['media', 'tags']);

            return Utilities::ok(new UserServiceResource($service));
            
        } catch(\Exception $e) {
            return Utilities::error($e, "An Error Occurred while attempting to Add Service Media");
        }
    }

    public function deleteMedia($id)
    {
        $file = $this->fileService->getFile($id);
        if(!$file) return Utilities::error402("Media not found");

        $this->fileService->delete($file);

        return Utilities::okay("Media deleted Successfully");
    }

    public function removeTag($serviceId, $tagId)
    {
        try{
            $this->service->removeServiceTag($serviceId, $tagId);

            return Utilities::okay("Service Tag removed");
        } catch(\Exception $e) {
            throw $e;
        }
    }

}
