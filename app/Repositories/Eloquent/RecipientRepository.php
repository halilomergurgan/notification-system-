<?php

namespace App\Repositories\Eloquent;

use App\Models\Recipient;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\RecipientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RecipientRepository extends BaseRepository implements RecipientRepositoryInterface
{
    /**
     * RecipientRepository constructor.
     *
     * @param Recipient $model
     */
    public function __construct(Recipient $model)
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
     * @return Recipient
     */
    public function find(int $id): Recipient
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return Recipient
     */
    public function create(array $data): Recipient
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Recipient
     */
    public function update(int $id, array $data): Recipient
    {
        $recipient = $this->find($id);
        $recipient->update($data);

        return $recipient;
    }
}
