<?php

namespace App\Repositories\Eloquent;

use App\Models\MessageQueue;
use App\Repositories\Interfaces\MessageQueueRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MessageQueueRepository extends BaseRepository implements MessageQueueRepositoryInterface
{
    /**
     * MessageQueueRepository constructor.
     *
     * @param MessageQueue $model
     */
    public function __construct(MessageQueue $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all records
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return parent::all();
    }

    /**
     * Delete record by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return parent::delete($id);
    }

    /**
     * @param int $id
     * @return MessageQueue
     */
    public function find(int $id): MessageQueue
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return MessageQueue
     */
    public function create(array $data): MessageQueue
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return MessageQueue
     */
    public function update(int $id, array $data): MessageQueue
    {
        $queue = $this->find($id);
        $queue->update($data);

        return $queue;
    }

    /**
     * @param int $limit
     * @return Collection
     */
    public function getPendingMessages(int $limit = 2): Collection
    {
        return $this->model->with(['message', 'recipient'])
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            })
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * @param int $id
     * @param string $providerMessageId
     * @param array $providerResponse
     * @return MessageQueue
     */
    public function markAsSent(int $id, string $providerMessageId, array $providerResponse): MessageQueue
    {
        $messageQueue = $this->find($id);
        $messageQueue->update([
            'status' => 'sent',
            'sent_at' => now(),
            'provider_message_id' => $providerMessageId,
            'provider_response' => $providerResponse
        ]);

        return $messageQueue;
    }

    /**
     * @param int $id
     * @param array $providerResponse
     * @return MessageQueue
     */
    public function markAsFailed(int $id, array $providerResponse): MessageQueue
    {
        $messageQueue = $this->find($id);
        $retryCount = $messageQueue->retry_count + 1;

        $messageQueue->update([
            'status' => $retryCount >= $messageQueue->max_retries ? 'failed' : 'pending',
            'retry_count' => $retryCount,
            'provider_response' => $providerResponse
        ]);

        return $messageQueue;
    }

    /**
     * @return Collection
     */
    public function getSentMessages(): Collection
    {
        return $this->model->with(['message', 'recipient'])
            ->where('status', 'sent')
            ->whereNotNull('provider_message_id')
            ->orderBy('sent_at', 'desc')
            ->get();
    }

    /**
     * @param int $id
     * @param $scheduledAt
     * @return void
     */
    public function updateScheduledAt(int $id, $scheduledAt): void
    {
        $this->model->where('id', $id)->update([
            'scheduled_at' => $scheduledAt
        ]);
    }
}
