<?php

namespace App\Repositories\Interfaces;

use App\Models\MessageQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface MessageQueueRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param int $id
     * @return MessageQueue
     */
    public function find(int $id): MessageQueue;

    /**
     * @param array $data
     * @return MessageQueue
     */
    public function create(array $data): MessageQueue;

    /**
     * @param int $id
     * @param array $data
     * @return MessageQueue
     */
    public function update(int $id, array $data): MessageQueue;

    /**
     * @param int $limit
     * @return Collection
     */
    public function getPendingMessages(int $limit = 2): Collection;

    /**
     * @param int $id
     * @param string $providerMessageId
     * @param array $providerResponse
     * @return MessageQueue
     */
    public function markAsSent(int $id, string $providerMessageId, array $providerResponse): MessageQueue;

    /**
     * @param int $id
     * @param array $providerResponse
     * @return MessageQueue
     */
    public function markAsFailed(int $id, array $providerResponse): MessageQueue;

    /**
     * @param int $id
     * @return MessageQueue
     */
    public function markAsCancelled(int $id): MessageQueue;

    /**
     * @param int $id
     * @param $scheduledAt
     * @return void
     */
    public function updateScheduledAt(int $id, $scheduledAt): void;

    /**
     * @param string $status
     * @param int $perPage
     * @return LengthAwarePaginator
 */
    public function getMessagesByStatus(string $status, int $perPage): LengthAwarePaginator;
}
