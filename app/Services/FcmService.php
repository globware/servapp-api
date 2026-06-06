<?php 

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;

class FcmService 
{
    
    public function publish(string $topic, string $message, array $meta)
    {
        $messaging = app('firebase.messaging');

        $message = CloudMessage::new()
            ->withData([
                'meta' => $meta,
                // 'sender_id' => (string) auth()->id(),
                'message' => $message,
                'created_at' => now()->toDateTimeString(),
            ])
            ->withTopic($topic);

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