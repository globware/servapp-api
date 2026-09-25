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

    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\UserProductRequestResource}
     */
    public function confirm($id)
    {
        $request = $this->requestService->getRequest($id);
        if (!$request || $request->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            return Utilities::error402("Request not found");
        }

        if ($request->Status !== 'provider_fulfilled') {
            return Utilities::error402("Request must be marked as fulfilled by provider first.");
        }

        $request = $this->requestService->changeCustomerStatus($id, \Illuminate\Support\Facades\Auth::id(), 'fulfilled');
        return Utilities::ok(new UserProductRequestResource($request), "Request confirmed successfully");
    }

    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\UserProductRequestResource}
     */
    public function cancel($id)
    {
        $request = $this->requestService->getRequest($id);
        if (!$request || $request->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            return Utilities::error402("Request not found");
        }

        if (in_array($request->Status, ['fulfilled', 'cancelled', 'provider_fulfilled', 'declined'])) {
            return Utilities::error402("Cannot cancel request in its current state.");
        }

        $request = $this->requestService->changeCustomerStatus($id, \Illuminate\Support\Facades\Auth::id(), 'cancelled');
        return Utilities::ok(new UserProductRequestResource($request), "Request cancelled successfully");
    }

    /**
     * @return array{status: boolean, message: string, data: \App\Models\UserProductReview}
     */
    public function review($id, \Illuminate\Http\Request $request)
    {
        $req = $this->requestService->getRequest($id);
        if (!$req || $req->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            return Utilities::error402("Request not found");
        }

        if ($req->Status !== 'fulfilled') {
            return Utilities::error402("Only fulfilled requests can be reviewed.");
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string'
        ]);

        $review = \App\Models\UserProductReview::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'user_product_id' => $req->user_product_id,
            'rating' => $validated['rating'],
            'review' => $validated['review'] ?? null,
        ]);

        return Utilities::ok($review, "Review submitted successfully");
    }

    /**
     * @return array{status: boolean, message: string, data: array}
     */
    public function read($id)
    {
        $req = $this->requestService->getRequest($id);
        if (!$req || $req->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            return Utilities::error402("Request not found");
        }

        // Standard mark read logic using the polymorph relations
        \App\Models\Chat::where('requestable_id', $id)
            ->where('requestable_type', \App\Models\UserProductRequest::class)
            ->where('receiver_id', \Illuminate\Support\Facades\Auth::id())
            ->update(['seen' => true]);

        return Utilities::ok([], "Messages marked as read");
    }
}
