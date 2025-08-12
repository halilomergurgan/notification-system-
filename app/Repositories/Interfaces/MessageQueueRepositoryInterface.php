<?php

namespace App\Repositories\Interfaces;

use App\Models\MessageQueue;
use Illuminate\Database\Eloquent\Collection;

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
}
