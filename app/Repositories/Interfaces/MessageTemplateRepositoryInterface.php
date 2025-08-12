<?php

namespace App\Repositories\Interfaces;

use App\Models\MessageTemplate;
use Illuminate\Database\Eloquent\Collection;

interface MessageTemplateRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param int $id
     * @return MessageTemplate
     */
    public function find(int $id): MessageTemplate;

    /**
     * @param array $data
     * @return MessageTemplate
     */
    public function create(array $data): MessageTemplate;

    /**
     * @param int $id
     * @param array $data
     * @return MessageTemplate
     */
    public function update(int $id, array $data): MessageTemplate;
}
