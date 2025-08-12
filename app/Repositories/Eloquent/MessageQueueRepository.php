<?php

namespace App\Repositories\Eloquent;

use App\Models\MessageQueue;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\MessageQueueRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MessageQueueRepository extends BaseRepository implements MessageQueueRepositoryInterface
{
    /**
     * @var MessageQueue
     */
    protected MessageQueue $model;

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
}
