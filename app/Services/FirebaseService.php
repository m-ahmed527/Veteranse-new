<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    public $factory;
    public $messaging;
    public $database;

    public function __construct()
    {
        $this->factory = (new Factory)->withServiceAccount(public_path("firebase/firebase_credentials.json"));
        $this->messaging = $this->factory->createMessaging();
        $this->database = $this->factory->createDatabase();
    }

    // Push notification bhejne ka method
    public function sendNotification($deviceToken, $title, $body)
    {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));
        return $this->messaging->send($message);
    }

    // Chat message save karne ka method
    public function saveChatMessage($roomId, $data)
    {
        $reference = $this->database->getReference('chats/' . $roomId);
        return $reference->push($data);
    }
}
