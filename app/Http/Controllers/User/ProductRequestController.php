<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserProductRequest;
use App\Models\UserProduct;
use App\Models\Chat;
use App\Models\User;
use App\Utilities;

class ProductRequestController extends Controller
{
    /**
     * Start a new product inquiry. This automatically spins up a Chat thread.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_product_id' => 'required|exists:user_products,id',
            'message' => 'required|string'
        ]);

        $product = UserProduct::find($validated['user_product_id']);

        // 1. Create the inquiry
        $inquiry = new UserProductRequest();
        $inquiry->user_product_id = $product->id;
        $inquiry->user_id = Auth::id();
        $inquiry->message = $validated['message'];
        $inquiry->Status = 'inquiry_sent';
        $inquiry->save();

        // 2. Initialize the Polymorphic Chat Thread
        // The first message is the inquiry message itself.
        $chat = new Chat();
        $chat->requestable_id = $inquiry->id;
        $chat->requestable_type = UserProductRequest::$type;
        $chat->sender_id = Auth::id();
        $chat->sender_type = User::$type;
        $chat->receiver_id = $product->user_id; // The provider
        $chat->receiver_type = User::$type;
        $chat->message = $validated['message'];
        $chat->save();

        return Utilities::ok([
            'request' => $inquiry,
            'chat' => $chat
        ]);
    }

    public function index()
    {
        $requests = UserProductRequest::where('user_id', Auth::id())->with('userProduct')->get();
        return Utilities::ok($requests);
    }

    public function show($id)
    {
        $request = UserProductRequest::where('user_id', Auth::id())->with('userProduct')->find($id);
        if (!$request) return Utilities::error402("Request not found");
        return Utilities::ok($request);
    }

    public function sendMessage(Request $request, $requestId)
    {
        $validated = $request->validate(['message' => 'required|string']);
        
        $inquiry = UserProductRequest::where('user_id', Auth::id())->find($requestId);
        if (!$inquiry) return Utilities::error402("Request not found");

        $product = UserProduct::find($inquiry->user_product_id);

        $chat = new Chat();
        $chat->requestable_id = $inquiry->id;
        $chat->requestable_type = UserProductRequest::$type;
        $chat->sender_id = Auth::id();
        $chat->sender_type = User::$type;
        $chat->receiver_id = $product->user_id;
        $chat->receiver_type = User::$type;
        $chat->message = $validated['message'];
        $chat->save();

        return Utilities::ok($chat);
    }

    public function getChats($requestId)
    {
        $inquiry = UserProductRequest::where('user_id', Auth::id())->find($requestId);
        if (!$inquiry) return Utilities::error402("Request not found");

        $chats = Chat::where('requestable_id', $inquiry->id)
                     ->where('requestable_type', UserProductRequest::$type)
                     ->orderBy('created_at', 'asc')
                     ->get();

        // Return chats ALONG with the current request status for the UI
        return Utilities::ok([
            'status' => $inquiry->Status,
            'chats' => $chats
        ]);
    }
}
