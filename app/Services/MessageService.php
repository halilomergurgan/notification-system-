<?php

namespace App\Services;

use App\Repositories\Interfaces\MessageQueueRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Exception;

class MessageService
{
    /**
     * @var MessageQueueRepositoryInterface
     */
    protected MessageQueueRepositoryInterface $messageQueueRepository;

    /**
     * @var WebhookService
     */
    protected WebhookService $webhookService;

    /**
     * @var CacheService
     */
    protected CacheService $cacheService;

    /**
     * MessageService constructor.
     *
     * @param MessageQueueRepositoryInterface $messageQueueRepository
     * @param WebhookService $webhookService
     * @param CacheService $cacheService
     */
    public function __construct(
        MessageQueueRepositoryInterface $messageQueueRepository,
        WebhookService $webhookService,
        CacheService $cacheService
    ) {
        $this->messageQueueRepository = $messageQueueRepository;
        $this->webhookService = $webhookService;
        $this->cacheService = $cacheService;
    }

    /**
     * @param int $limit
     * @return array
     */
    public function processPendingMessages(int $limit = 2): array
    {
        $messages = $this->messageQueueRepository->getPendingMessages($limit);

        $results = [];

        foreach ($messages as $messageQueue) {
            try {
                $this->sendMessage($messageQueue);

                $results[] = [
                    'id' => $messageQueue->id,
                    'status' => 'dispatched'
                ];
            } catch (Exception $e) {
                Log::error('Failed to dispatch message', [
                    'message_queue_id' => $messageQueue->id,
                    'error' => $e->getMessage()
                ]);

                $results[] = [
                    'id' => $messageQueue->id,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * @param $messageQueue
     * @return array
     * @throws Exception
     */
    protected function sendMessage($messageQueue): array
    {
        $this->messageQueueRepository->updateScheduledAt($messageQueue->id, now());

        $recipient = $messageQueue->recipient;
        $message = $messageQueue->message;

        if ($message->type === 'sms' && $message->character_count > 360) {
            $this->messageQueueRepository->markAsFailed(
                $messageQueue->id,
                []
            );

            return ['status' => 'failed', 'messageId' => $message->id];
        }

        $response = $this->webhookService->sendMessage(
            $recipient->country_code . $recipient->phone_number,
            $messageQueue->personalized_content ?? $message->content
        );

        Log::info('Webhook response', [
            'message_queue_id' => $messageQueue->id,
            'response' => $response
        ]);

        if (isset($response['messageId']) && isset($response['status']) && $response['status'] === 'sent') {

           $this->messageQueueRepository->markAsSent(
                $messageQueue->id,
                $response['messageId'],
                $response
            );

            $this->cacheService->storeMessageInfo(
                $response['messageId'],
                $messageQueue->id,
                now()
            );

            return ['status' => 'sent', 'messageId' => $response['messageId']];
        } else {
            $this->messageQueueRepository->markAsFailed(
                $messageQueue->id,
                $response
            );
        }
    }

    /**
     * @return array
     */
    public function getSentMessages(): array
    {
        $messages = $this->messageQueueRepository->getSentMessages();

        return $messages->map(function ($messageQueue) {
            return [
                'id' => $messageQueue->id,
                'message_id' => $messageQueue->message_id,
                'recipient' => $messageQueue->recipient->country_code . $messageQueue->recipient->phone_number,
                'content' => $messageQueue->personalized_content ?? $messageQueue->message->content,
                'provider_message_id' => $messageQueue->provider_message_id,
                'sent_at' => $messageQueue->sent_at->toIso8601String(),
                'status' => $messageQueue->status
            ];
        })->toArray();
    }
}
