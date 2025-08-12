<?php

namespace App\Repositories\Interfaces;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

interface MessageRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param int $id
     * @return Message
     */
    public function find(int $id): Message;

    /**
     * @param array $data
     * @return Message
     */
    public function create(array $data): Message;

    /**
     * @param int $id
     * @param array $data
     * @return Message
     */
    public function update(int $id, array $data): Message;

}
