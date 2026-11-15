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
            ->with(['userProduct.media', 'userProduct.product', 'userProduct.user', 'user', 'chats'])
            ->get();
    }

    public function getProviderRequests($userId)
    {
        return UserProductRequest::whereHas('userProduct', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['userProduct.media', 'userProduct.product', 'userProduct.user', 'user', 'chats'])->get();
    }

    public function getUserRequest($id, $userId)
    {
        return UserProductRequest::where('user_id', $userId)
            ->with(['userProduct.media', 'userProduct.product', 'userProduct.user', 'user', 'chats'])
            ->find($id);
    }

    public function getProviderRequest($id, $userId)
    {
        return UserProductRequest::whereHas('userProduct', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['userProduct.media', 'userProduct.product', 'userProduct.user', 'user', 'chats'])->find($id);
    }

    public function createRequest(array $data, $userId)
    {
        $productId = $data['user_product_id'] ?? $data['productId'] ?? $data['userProductId'] ?? $data['product_id'] ?? null;
        $product = UserProduct::find($productId);
        if (!$product) throw new AppException("Product not found");

        if ($product->user_id == $userId) {
            throw new AppException("You cannot request your own product");
        }

        $message = is_array($data['message'] ?? null) ? ($data['message']['message'] ?? '') : ($data['message'] ?? '');

        $request = new UserProductRequest();
        $request->user_id = $userId;
        $request->user_product_id = $product->id;
        $request->message = $message ?? '';
        $request->Status = 'inquiry_sent';
        $request->save();

        if (!empty($message) || !empty($data['mediaIds'])) {
            $this->sendMessage($request, $data, $userId, $product->user_id);
        }

        return $request->load(['userProduct.media', 'userProduct.product', 'userProduct.user', 'user', 'chats']);
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

        $messageText = is_array($data) ? ($data['message'] ?? null) : $data;
        $latitude = is_array($data) ? ($data['latitude'] ?? null) : null;
        $longitude = is_array($data) ? ($data['longitude'] ?? null) : null;
        $mediaIds = is_array($data) ? ($data['mediaIds'] ?? []) : [];

        $chat = new \App\Models\Chat();
        $chat->requestable_id = $inquiry->id;
        $chat->requestable_type = \App\Models\UserProductRequest::class;
        $chat->sender_id = $senderId;
        $chat->sender_type = \App\Models\User::class;
        $chat->receiver_id = $receiverId;
        $chat->receiver_type = \App\Models\User::class;
        $chat->message = $messageText;
        $chat->latitude = $latitude;
        $chat->longitude = $longitude;
        $chat->save();

        if (!empty($mediaIds)) {
            $chat->media()->sync($mediaIds);
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
