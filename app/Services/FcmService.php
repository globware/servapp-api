<?php 

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;

class FcmService 
{
    
    public function publish(array $data)
    {
        $messaging = app('firebase.messaging');

        $message = CloudMessage::new()
            ->withData([
                'meta' => json_encode($data['meta']),
                'message' => $data['message'],
                'type' => $data['type'],
                'title' => $data['title'],
                'created_at' => now()->toDateTimeString(),
            ])
            ->withTopic($data['topic']);

        $messaging->send($message);
    }

    public function send($user, $message, $chatId)
    {
        $tokens = $user->fcmTokens()
        ->pluck('token')
        ->toArray();

        // Send FCM push
        if (!empty($tokens)) {
            $messaging = app('firebase.messaging');

            $fcmMessage = CloudMessage::new()->withData([
                'type' => 'chat_message',
                'chat_id' => (string) $chatId,
                // 'sender_id' => (string) auth()->id(),
                'message' => $message,
                'created_at' => now()->toDateTimeString(),
            ]);

            $messaging->sendMulticast($fcmMessage, $tokens);
        }
    }
}