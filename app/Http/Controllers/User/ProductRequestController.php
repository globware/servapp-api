<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\UserProductRequestService;
use App\Http\Resources\UserProductRequestResource;
use App\Http\Requests\User\SendProductMessageRequest;
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
        $requests = $this->requestService->getUserRequests(Auth::id());
        return Utilities::ok(UserProductRequestResource::collection($requests));
    }



    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\UserProductRequestResource}
     */
    public function show($id)
    {
        $request = $this->requestService->getUserRequest($id, Auth::id());
        if (!$request) return Utilities::error402("Request not found");
        return Utilities::ok(new UserProductRequestResource($request));
    }



    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\UserProductRequestResource}
     */
    public function store(SendProductMessageRequest $request)
    {
        try {
            $inquiry = $this->requestService->createRequest($request->validated(), Auth::id());
            return Utilities::ok(new UserProductRequestResource($inquiry));
        } catch (\Exception $e) {
            return Utilities::error($e, $e->getMessage());
        }
    }



    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\ChatResource}
     */
    public function sendMessage(SendProductMessageRequest $request, $requestId)
    {
        $inquiry = $this->requestService->getUserRequest($requestId, Auth::id());
        if (!$inquiry) return Utilities::error402("Request not found");

        $chat = $this->requestService->sendMessage($inquiry, $request->message, Auth::id(), $inquiry->userProduct->user_id);
        return Utilities::ok(new ChatResource($chat));
    }



    /**
     * @return array{status: boolean, message: string, data: array{status: string, chats: array<\App\Http\Resources\ChatResource>}}
     */
    public function getChats($requestId)
    {
        $inquiry = $this->requestService->getUserRequest($requestId, Auth::id());
        if (!$inquiry) return Utilities::error402("Request not found");

        $chats = $this->requestService->getChats($inquiry);
        return Utilities::ok([
            'status' => $inquiry->Status,
            'chats' => ChatResource::collection($chats)
        ]);
    }
}
