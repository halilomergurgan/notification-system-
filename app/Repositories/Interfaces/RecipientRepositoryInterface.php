<?php

namespace App\Repositories\Interfaces;

use App\Models\Recipient;
use Illuminate\Database\Eloquent\Collection;

interface RecipientRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param int $id
     * @return Recipient
     */
    public function find(int $id): Recipient;

    /**
     * @param array $data
     * @return Recipient
     */
    public function create(array $data): Recipient;

    /**
     * @param int $id
     * @param array $data
     * @return Recipient
     */
    public function update(int $id, array $data): Recipient;
}
