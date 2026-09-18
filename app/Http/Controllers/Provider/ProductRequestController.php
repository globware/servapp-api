<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Provider\SendProductMessageRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\UserProductRequest;
use App\Models\UserProduct;
use App\Models\Chat;
use App\Models\User;
use App\Utilities;

class ProductRequestController extends Controller
{
    public function index()
    {
        // Get all requests for products owned by this provider
        $requests = UserProductRequest::whereHas('userProduct', function($query) {
            $query->where('user_id', Auth::id());
        })->with('userProduct', 'user')->get();

        return Utilities::ok($requests);
    }

    public function show($id)
    {
        $request = UserProductRequest::whereHas('userProduct', function($query) {
            $query->where('user_id', Auth::id());
        })->with('userProduct', 'user')->find($id);

        if (!$request) return Utilities::error402("Request not found or unauthorized");
        return Utilities::ok($request);
    }

    public function accept($id)
    {
        $request = UserProductRequest::whereHas('userProduct', function($query) {
            $query->where('user_id', Auth::id());
        })->find($id);

        if (!$request) return Utilities::error402("Request not found");

        $request->Status = 'accepted';
        $request->save();

        return Utilities::ok($request);
    }

    public function decline($id)
    {
        $request = UserProductRequest::whereHas('userProduct', function($query) {
            $query->where('user_id', Auth::id());
        })->find($id);

        if (!$request) return Utilities::error402("Request not found");

        $request->Status = 'declined';
        $request->save();

        return Utilities::ok($request);
    }

    public function fulfill($id)
    {
        $request = UserProductRequest::whereHas('userProduct', function($query) {
            $query->where('user_id', Auth::id());
        })->find($id);

        if (!$request) return Utilities::error402("Request not found");

        $request->Status = 'provider_fulfilled';
        $request->save();

        return Utilities::ok($request);
    }

    public function sendMessage(SendProductMessageRequest $request, $requestId)
    {
        $validated = $request->validated();
        
        $inquiry = UserProductRequest::whereHas('userProduct', function($query) {
            $query->where('user_id', Auth::id());
        })->find($requestId);

        if (!$inquiry) return Utilities::error402("Request not found");

        $chat = new Chat();
        $chat->requestable_id = $inquiry->id;
        $chat->requestable_type = UserProductRequest::$type;
        $chat->sender_id = Auth::id();
        $chat->sender_type = User::$type; // Assuming Providers act as Users in this context
        $chat->receiver_id = $inquiry->user_id; // The customer
        $chat->receiver_type = User::$type;
        $chat->message = $validated['message'];
        $chat->save();

        return Utilities::ok($chat);
    }

    public function getChats($requestId)
    {
        $inquiry = UserProductRequest::whereHas('userProduct', function($query) {
            $query->where('user_id', Auth::id());
        })->find($requestId);

        if (!$inquiry) return Utilities::error402("Request not found");

        $chats = Chat::where('requestable_id', $inquiry->id)
                     ->where('requestable_type', UserProductRequest::$type)
                     ->orderBy('created_at', 'asc')
                     ->get();

        return Utilities::ok([
            'status' => $inquiry->Status,
            'chats' => $chats
        ]);
    }
}
