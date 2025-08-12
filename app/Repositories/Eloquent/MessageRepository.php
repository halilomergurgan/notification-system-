<?php

namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{
    /**
     * MessageRepository constructor.
     *
     * @param Message $model
     */
    public function __construct(Message $model)
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
     * @return Message
     */
    public function find(int $id): Message
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return Message
     */
    public function create(array $data): Message
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Message
     */
    public function update(int $id, array $data): Message
    {
        $message = $this->find($id);
        $message->update($data);

        return $message;
    }

}
