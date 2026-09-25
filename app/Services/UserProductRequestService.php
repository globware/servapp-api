<?php

namespace App\Services;

use App\Models\UserProductRequest;
use App\Models\Chat;
use App\Models\User;
use App\Models\UserProduct;
use App\Exceptions\AppException;

class UserProductRequestService
{
    public function getUserRequests($userId)
    {
        return UserProductRequest::where('user_id', $userId)
            ->with('userProduct', 'user')
            ->get();
    }

    public function getProviderRequests($userId)
    {
        return UserProductRequest::whereHas('userProduct', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('userProduct', 'user')->get();
    }

    public function getUserRequest($id, $userId)
    {
        return UserProductRequest::where('user_id', $userId)
            ->with('userProduct', 'user')
            ->find($id);
    }

    public function getProviderRequest($id, $userId)
    {
        return UserProductRequest::whereHas('userProduct', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('userProduct', 'user')->find($id);
    }

    public function createRequest(array $data, $userId)
    {
        $product = UserProduct::find($data['user_product_id']);
        if (!$product) throw new AppException("Product not found");

        $request = new UserProductRequest();
        $request->user_id = $userId;
        $request->user_product_id = $data['user_product_id'];
        $request->Status = 'inquiry_sent';
        $request->save();

        $this->sendMessage($request, $data['message'], $userId, clone $product->user_id);

        return $request->load('userProduct', 'user');
    }

    public function changeStatus($id, $userId, $status)
    {
        $request = $this->getProviderRequest($id, $userId);
        if (!$request) throw new AppException("Request not found or unauthorized");

        $request->Status = $status;
        $request->save();

        return $request;
    }

    public function sendMessage($inquiry, $data, $senderId, $receiverId)
    {
        if (\App\Models\UserBlock::where('user_id', $senderId)->where('blocked_user_id', $receiverId)->orWhere('user_id', $receiverId)->where('blocked_user_id', $senderId)->exists()) {
            throw new \App\Exceptions\AppException("Action blocked by user settings.");
        }
        $chat = new \App\Models\Chat();
        $chat->requestable_id = $inquiry->id;
        $chat->requestable_type = \App\Models\UserProductRequest::class;
        $chat->sender_id = $senderId;
        $chat->sender_type = \App\Models\User::class;
        $chat->receiver_id = $receiverId;
        $chat->receiver_type = \App\Models\User::class;
        $chat->message = $data['message'] ?? null;
        $chat->latitude = $data['latitude'] ?? null;
        $chat->longitude = $data['longitude'] ?? null;
        $chat->save();

        if (!empty($data['mediaIds'])) {
            $chat->media()->sync($data['mediaIds']);
        }

        return $chat;
    }

    public function getChats($inquiry)
    {
        return Chat::where('requestable_id', $inquiry->id)
                     ->where('requestable_type', UserProductRequest::$type)
                     ->orderBy('created_at', 'asc')
                     ->get();
    }

    public function changeCustomerStatus($id, $userId, $status)
    {
        $request = $this->getRequest($id);
        if (!$request || $request->user_id !== $userId) {
            throw new AppException("Request not found or unauthorized");
        }

        $request->Status = $status;
        $request->save();
        return $request;
    }
}
