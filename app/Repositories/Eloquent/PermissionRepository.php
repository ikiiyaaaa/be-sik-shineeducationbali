<?php

namespace App\Repositories\Eloquent;

use App\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;

class PermissionRepository implements PermissionRepositoryInterface
{
    protected $model;

    public function __construct(Permission $model)
    {
        $this->model = $model;
    }

    /**
     * Mengambil semua permissions.
     *
     * @return mixed
     */
    public function getAllPermissions()
    {
        return $this->model->all();
    }

    /**
     * Mengambil permission berdasarkan ID.
     *
     * @param int $id
     * @return mixed
     */
    public function getPermissionById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Mengambil permission berdasarkan nama.
     *
     * @param string $name
     * @return mixed
     */
    public function getPermissionByName($name)
    {
        return $this->model->where('name', $name)->first();
    }

    /**
     * Mengambil permission berdasarkan status.
     *
     * @param string $status
     * @return mixed
     */
    public function getPermissionByStatus($status)
    {
        return $this->model->byStatus($status)->get();
    }

    /**
     * Membuat permission baru.
     *
     * @param array $data
     * @return mixed
     */
    public function createPermission(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Memperbarui permission berdasarkan ID.
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updatePermission($id, array $data)
    {
        $permission = $this->getPermissionById($id);
        $permission->update($data);
        return $permission;
    }

    /**
     * Menghapus permission berdasarkan ID.
     *
     * @param int $id
     * @return mixed
     */
    public function deletePermission($id)
    {
        $permission = $this->getPermissionById($id);
        return $permission->delete();
    }

    /**
     * Mengupdate permission status.
     *
     * @param int $id
     * @param string $status
     * @return mixed
     */
    public function updatePermissionStatus($id, $status)
    {
        $permission = $this->getPermissionById($id);
        $permission->update(['status' => $status]);
        return $permission;
    }
}
