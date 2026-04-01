<?php 

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;

class FcmService 
{
    
    public function publish($topic, $message, $chatId)
    {
        $messaging = app('firebase.messaging');

        $message = CloudMessage::new()
            ->withData([
                'type' => 'chat_message',
                'chat_id' => (string) $chatId,
                // 'sender_id' => (string) auth()->id(),
                'message' => $message,
                'created_at' => now()->toDateTimeString(),
            ])
            ->withTarget('topic', $topic);

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