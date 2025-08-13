<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WebhookService
{
    /**
     * @var string
     */
    protected string $webhookUrl;

    /**
     * @var string
     */
    protected string $authKey;

    public function __construct()
    {
        $this->webhookUrl =  env('WEBHOOK_URL', '');
        $this->authKey = env('WEBHOOK_AUTH_KEY', '1234567890');
    }

    /**
     * @param string $phoneNumber
     * @param string $content
     * @return array
     * @throws Exception
     */
    public function sendMessage(string $phoneNumber, string $content): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-ins-auth-key' => $this->authKey
            ])->post($this->webhookUrl, [
                'to' => $phoneNumber,
                'content' => $content
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Webhook request failed: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Webhook service error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
