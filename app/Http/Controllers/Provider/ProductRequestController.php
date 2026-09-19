<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\UserProductRequestService;
use App\Http\Resources\UserProductRequestResource;
use App\Http\Requests\Provider\SendProductMessageRequest;
use App\Http\Resources\ChatResource;
use App\Utilities;

class ProductRequestController extends Controller
{
    public function __construct(protected UserProductRequestService $requestService)
    {
    }



    /**
     * @return array{status: boolean, message: string, data: array<\App\Http\Resources\UserProductRequestResource>}
     */
    public function index()
    {
        $requests = $this->requestService->getProviderRequests(Auth::id());
        return Utilities::ok(UserProductRequestResource::collection($requests));
    }



    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\UserProductRequestResource}
     */
    public function show($id)
    {
        $request = $this->requestService->getProviderRequest($id, Auth::id());
        if (!$request) return Utilities::error402("Request not found");
        return Utilities::ok(new UserProductRequestResource($request));
    }



    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\UserProductRequestResource}
     */
    public function accept($id)
    {
        try {
            $request = $this->requestService->changeStatus($id, Auth::id(), 'accepted');
            return Utilities::ok(new UserProductRequestResource($request));
        } catch (\Exception $e) {
            return Utilities::error($e, $e->getMessage());
        }
    }



    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\UserProductRequestResource}
     */
    public function decline($id)
    {
        try {
            $request = $this->requestService->changeStatus($id, Auth::id(), 'declined');
            return Utilities::ok(new UserProductRequestResource($request));
        } catch (\Exception $e) {
            return Utilities::error($e, $e->getMessage());
        }
    }



    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\UserProductRequestResource}
     */
    public function fulfill($id)
    {
        try {
            $request = $this->requestService->changeStatus($id, Auth::id(), 'provider_fulfilled');
            return Utilities::ok(new UserProductRequestResource($request));
        } catch (\Exception $e) {
            return Utilities::error($e, $e->getMessage());
        }
    }



    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\ChatResource}
     */
    public function sendMessage(SendProductMessageRequest $request, $requestId)
    {
        $inquiry = $this->requestService->getProviderRequest($requestId, Auth::id());
        if (!$inquiry) return Utilities::error402("Request not found");

        $chat = $this->requestService->sendMessage($inquiry, $request->message, Auth::id(), $inquiry->user_id);
        return Utilities::ok(new ChatResource($chat));
    }



    /**
     * @return array{status: boolean, message: string, data: array{status: string, chats: array<\App\Http\Resources\ChatResource>}}
     */
    public function getChats($requestId)
    {
        $inquiry = $this->requestService->getProviderRequest($requestId, Auth::id());
        if (!$inquiry) return Utilities::error402("Request not found");

        $chats = $this->requestService->getChats($inquiry);
        return Utilities::ok([
            'status' => $inquiry->Status,
            'chats' => ChatResource::collection($chats)
        ]);
    }
}
