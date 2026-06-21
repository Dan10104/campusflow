<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $serviceAccountPath = storage_path('gestion-activos-mobile-firebase-adminsdk-fbsvc-44cda19f4c.json');
        
        if (!file_exists($serviceAccountPath)) {
            Log::error("Firebase service account file not found at: " . $serviceAccountPath);
            return;
        }

        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Enviar notificación a un token específico
     */
    public function sendNotification(string $deviceToken, string $title, string $body, array $data = [])
    {
        if (!$this->messaging) {
            Log::error("Firebase Messaging not initialized.");
            return false;
        }

        try {
            $notification = Notification::create($title, $body);
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification)
                ->withData($data);

            $this->messaging->send($message);
            Log::info("Notificación enviada a $deviceToken: $title");
            return true;
        } catch (\Exception $e) {
            Log::error("Error enviando notificación Firebase: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar notificación a múltiples tokens
     */
    public function sendToMultiple(array $tokens, string $title, string $body, array $data = [])
    {
        if (!$this->messaging) return false;

        $tokens = array_filter($tokens); // Limpiar nulos
        if (empty($tokens)) return false;

        foreach ($tokens as $token) {
            $this->sendNotification($token, $title, $body, $data);
        }

        return true;
    }
}
