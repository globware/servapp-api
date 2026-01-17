<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;

use App\Exceptions\AppException;

class MessageService
{
    public $read = null;

    public function getMessage($id, $with=[])
    {
        return Message::with($with)->where("id", $id)->first();
    }

    public function send($data)
    {
        try{
            $message = new Message;
            $message->sender_id = $data['senderId'];
            $message->sender_type = $data['senderType'];
            $message->receiver_id = $data['receiverId'];
            $message->receiver_type = $data['receiverType'];
            $message->message = $data['message'];
            $message->save();

            return $message;
        }catch(\Exception $e){
            throw new AppException(500, null, $e);
        }
    }

    public function markAsRead($entity, $entityClass)
    {
        try{
            // $messages = $this->getEntityMessages($entity, $entityClass);
            $messages = Message::where('receiver_id', $entity->id)
                        ->where('receiver_type', $entityClass::$type)
                        ->when($this->read !== null, function ($query) {
                            $query->where('read', $this->read);
                        })->get();
            if($messages->count() > 0) {
                foreach($messages as $message) {
                    $message->read = true;
                    $message->update();
                }
            }
        } catch (\Exception $e) {
            throw new AppException(500, null, $e);
        }
    }

    public function getEntityMessages($entity, $entityClass)
    {
        $messages = Message::where(function($query) use ($entity, $entityClass) {
            $query->where(function($q) use ($entity, $entityClass) {
                $q->where('sender_id', $entity->id)
                  ->where('sender_type', $entityClass::$type);
            })->orWhere(function($q) use ($entity, $entityClass) {
                $q->where('receiver_id', $entity->id)
                  ->where('receiver_type', $entityClass::$type);
            });
        })
        ->when($this->read !== null, function ($query) {
            $query->where('read', $this->read);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'desc')
        ->get();

        return $messages;
    }

    public function getConversations($entity, $entityClass)
    {
        try {
            // Get all messages where user is sender or receiver
            $messages = $this->getEntityMessages($entity, $entityClass);

            // Group messages by the other party
            $conversations = [];
            
            foreach ($messages as $message) {
                // Determine the other party
                if ($message->sender_id == $entity->id && $message->sender_type == $entityClass::$type) {
                    // User is sender, other party is receiver
                    $otherPartyId = $message->receiver_id;
                    $otherPartyType = $message->receiver_type;
                    $otherParty = $message->receiver;
                } else {
                    // User is receiver, other party is sender
                    $otherPartyId = $message->sender_id;
                    $otherPartyType = $message->sender_type;
                    $otherParty = $message->sender;
                }

                // Create a unique key for the conversation
                $conversationKey = $otherPartyType . '_' . $otherPartyId;

                // Initialize conversation if it doesn't exist
                if (!isset($conversations[$conversationKey])) {
                    $conversations[$conversationKey] = [
                        // 'otherParty' => $otherParty,
                        'otherPartyType' => $otherPartyType,
                        'otherPartyId' => $otherPartyId,
                        'name' => $otherParty->name,
                        'messages' => [],
                        'latestMessage' => null,
                        'unreadCount' => 0,
                    ];
                }

                // Add message to conversation
                $conversations[$conversationKey]['messages'][] = $message;

                // Update latest message if this is more recent
                if (!$conversations[$conversationKey]['latestMessage'] || 
                    $message->created_at > $conversations[$conversationKey]['latestMessage']->created_at) {
                    $conversations[$conversationKey]['latestMessage'] = $message;
                }

                // Count unread messages (where user is receiver and message is unread)
                if ($message->receiver_id == $entity->id && 
                    $message->receiver_type == $entityClass && 
                    !$message->read) {
                    $conversations[$conversationKey]['unreadCount']++;
                }
            }

            // Convert to array and sort by latest message date
            $conversationsArray = array_values($conversations);
            usort($conversationsArray, function($a, $b) {
                if (!$a['latestMessage']) return 1;
                if (!$b['latestMessage']) return -1;
                return $b['latestMessage']->created_at <=> $a['latestMessage']->created_at;
            });

            return $conversationsArray;
        } catch (\Exception $e) {
            throw new AppException(500, null, $e);
        }
    }
}