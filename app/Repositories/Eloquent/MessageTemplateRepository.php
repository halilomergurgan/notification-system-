<?php

namespace App\Repositories\Eloquent;

use App\Models\MessageTemplate;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\MessageTemplateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MessageTemplateRepository extends BaseRepository implements MessageTemplateRepositoryInterface
{
    /**
     * @var MessageTemplate
     */
    protected MessageTemplate $model;

    /**
     * MessageTemplateRepository constructor.
     *
     * @param MessageTemplate $model
     */
    public function __construct(MessageTemplate $model)
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
     * @return MessageTemplate
     */
    public function find(int $id): MessageTemplate
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return MessageTemplate
     */
    public function create(array $data): MessageTemplate
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return MessageTemplate
     */
    public function update(int $id, array $data): MessageTemplate
    {
        $template = $this->find($id);
        $template->update($data);
        return $template;
    }
}
