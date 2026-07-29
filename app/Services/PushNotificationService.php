<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PushNotificationService
{
    protected string $apiKey;
    protected bool $enabled;

    public function __construct()
    {
        $this->apiKey = config('services.pushengage.key');
        $this->enabled = (bool) config('services.pushengage.enabled', true);
    }

    public function send(string $title, string $message, string $url): array
    {
        if (!$this->enabled) {
            return ['skipped' => true, 'reason' => 'Push disabled'];
        }

        if (!$this->apiKey) {
            return ['error' => 'PushEngage API key missing'];
            
        }

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.pushengage.com/v1/notifications', [
            'title' => $title,
            'message' => $message,
            'url' => $url,
        ]);

        if ($response->failed()) {
            return [
                'error' => 'Push notification failed',
                'status' => $response->status(),
                'body' => $response->json(),
            ];
        }

        return $response->json() ?? [];
    }
}
