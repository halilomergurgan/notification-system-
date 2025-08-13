<?php

namespace App\Services;

use App\Models\MessageQueue;
use App\Repositories\Interfaces\MessageQueueRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
            $this->messageQueueRepository->markAsCancelled($messageQueue->id);

            return ['status' => MessageQueue::STATUS_CANCELLED, 'messageId' => $message->id];
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

            return [
                'status' => 'failed',
                'messageId' => $message->id,
                'error' => $response['error'] ?? 'Unknown error'
            ];
        }
    }

    /**
     * @param string $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getMessagesByStatus(string $status = 'sent', int $perPage = 20): LengthAwarePaginator
    {
        return $this->messageQueueRepository->getMessagesByStatus($status, $perPage);
    }
}
