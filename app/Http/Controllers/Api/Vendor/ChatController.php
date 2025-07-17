<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

class ChatController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function sendMessage(Request $request)
    {
        // dd($request->all());
        $user1 = $request->input('user1');
        $user2 = $request->input('user2');
        $message = $request->input('message');
        $type = $request->input('type');
        $senderId = $request->input('senderId');

        if (!$user1 || !$user2 || !$message || !$type || !$senderId) {
            return response()->json([
                'error' => 'user1, user2, message, type, and senderId are required'
            ], 400);
        }

        $chatId = collect([$user1, $user2])->sort()->implode('_');
        $receiverId = $senderId == $user1 ? $user2 : $user1;

        // Check if chat exists
        $chatRef = $this->firebase->database->getReference('chats/' . $chatId);
        $chatData = $chatRef->getValue();
        $isNewChat = !$chatData;

        if ($isNewChat) {
            $chatRef->set([
                'users' => [$user1, $user2],
                'createdAt' => now()->timestamp * 1000,
                'latest_message' => null,
                'unread_counts' => [
                    $user1 => 0,
                    $user2 => 0,
                ],
            ]);
            $this->updateUserChatIds($user1, $chatId);
            $this->updateUserChatIds($user2, $chatId);
        }

        $msgData = [
            'id' => (string) (now()->timestamp * 1000),
            'senderId' => $senderId,
            'message' => $message,
            'type' => $type,
            'timestamp' => now()->timestamp * 1000,
        ];

        // Get or create messages for this chat
        $messagesRef = $this->firebase->database->getReference('messages/' . $chatId);
        $messagesData = $messagesRef->getValue();

        if (!$messagesData) {
            $messagesRef->set([
                'messages' => [$msgData],
                'createdAt' => now()->timestamp * 1000,
            ]);
        } else {
            $updatedMessages = array_merge($messagesData['messages'] ?? [], [$msgData]);
            $messagesRef->update([
                'messages' => $updatedMessages,
                'updatedAt' => now()->timestamp * 1000,
            ]);
        }

        // Update chat node with latest message and unread count
        $updateData = [
            'latest_message' => $msgData,
            'updatedAt' => now()->timestamp * 1000,
        ];

        if ($chatData) {
            $currentUnreadCounts = $chatData['unread_counts'] ?? [
                $user1 => 0,
                $user2 => 0,
            ];
            $latestMessage = $chatData['latest_message'] ?? null;

            if ($latestMessage && ($latestMessage['senderId'] ?? null) == $senderId) {
                $newUnreadCount = ($currentUnreadCounts[$receiverId] ?? 0) + 1;
            } else {
                $newUnreadCount = 1;
            }

            $updateData['unread_counts'] = [
                ...$currentUnreadCounts,
                $receiverId => $newUnreadCount,
                $senderId => 0,
            ];
        } else {
            $updateData['unread_counts'] = [
                $user1 => $senderId == $user1 ? 0 : 1,
                $user2 => $senderId == $user2 ? 0 : 1,
            ];
        }

        $chatRef->update($updateData);

        // Send push notification to receiver
        /*
        try {
            $senderRef = $this->firebase->database->getReference('users/' . $senderId);
            $senderData = $senderRef->getValue();
            $senderName = $senderData['name'] ?? 'Someone';

            $notificationData = [
                'type' => 'chat_message',
                'chatId' => $chatId,
                'messageId' => $msgData['id'],
                'senderId' => $senderId,
                'senderName' => $senderName,
                'message' => $message,
                'messageType' => $type,
            ];

            // Yahan aapko receiver ka FCM token chahiye hoga
            $receiverRef = $this->firebase->database->getReference('users/' . $receiverId);
            $receiverData = $receiverRef->getValue();
            $receiverToken = $receiverData['fcm_token'] ?? null;

            if ($receiverToken) {
                $this->firebase->sendNotification(
                    $receiverToken,
                    $senderName,
                    $message,
                    $notificationData
                );
            }
        } catch (\Exception $e) {
            // Notification fail ho to bhi main request fail na ho
        }
        */

        return response()->json([
            'message' => 'Message sent successfully',
            'chatId' => $chatId,
            'messageId' => $msgData['id'],
            'unreadCount' => $updateData['unread_counts'][$receiverId] ?? 0,
            'isNewChat' => $isNewChat,
            'notificationSent' => true,
        ]);
    }

    // Helper function to update user's chatIds array
    private function updateUserChatIds($userId, $chatId)
    {
        $userRef = $this->firebase->database->getReference('users/' . $userId);
        $userData = $userRef->getValue();

        if ($userData) {
            $currentChatIds = $userData['chatIds'] ?? [];
            if (!in_array($chatId, $currentChatIds)) {
                $updatedChatIds = array_merge($currentChatIds, [$chatId]);
                $userRef->update([
                    'chatIds' => $updatedChatIds,
                    'updatedAt' => now()->timestamp * 1000,
                ]);
            }
        } else {
            $userRef->set([
                'chatIds' => [$chatId],
                'createdAt' => now()->timestamp * 1000,
                'updatedAt' => now()->timestamp * 1000,
            ]);
        }
    }

    // API to reset unread count for a user in a chat
    public function resetUnreadCount(Request $request)
    {
        $chatId = $request->input('chatId');
        $userId = $request->input('userId');

        if (!$chatId || !$userId) {
            return response()->json(['error' => 'chatId and userId are required'], 400);
        }

        $chatRef = $this->firebase->database->getReference('chats/' . $chatId);
        $chatData = $chatRef->getValue();

        if (!$chatData) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        $unreadCounts = $chatData['unread_counts'] ?? [];
        $unreadCounts[$userId] = 0;

        $chatRef->update([
            'unread_counts' => $unreadCounts,
            'updatedAt' => now()->timestamp * 1000,
        ]);

        return response()->json([
            'message' => 'Unread count reset successfully',
            'chatId' => $chatId,
            'userId' => $userId,
        ]);
    }
}
