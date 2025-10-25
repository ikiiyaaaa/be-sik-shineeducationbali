<?php

namespace App\Services\Implementations;

use Illuminate\Support\Facades\Cache;
use App\Services\Contracts\PermissionServiceInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;

class PermissionService implements PermissionServiceInterface
{
    protected $repository;

    const PERMISSIONS_ALL_CACHE_KEY = 'permissions.all';
    const PERMISSIONS_ACTIVE_CACHE_KEY = 'permissions.active';
    const PERMISSIONS_INACTIVE_CACHE_KEY = 'permissions.inactive';

    public function __construct(PermissionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Mengambil semua permissions.
     *
     * @return mixed
     */
    public function getAllPermissions()
    {
        return Cache::remember(self::PERMISSIONS_ALL_CACHE_KEY, 3600, function () {
            return $this->repository->getAllPermissions();
        });
    }

    /**
     * Mengambil permission berdasarkan ID.
     *
     * @param int $id
     * @return mixed
     */
    public function getPermissionById($id)
    {
        return $this->repository->getPermissionById($id);
    }

    /**
     * Mengambil permission berdasarkan nama.
     *
     * @param string $name
     * @return mixed
     */
    public function getPermissionByName($name)
    {
        return $this->repository->getPermissionByName($name);
    }

    /**
     * Mengambil permission berdasarkan status.
     *
     * @param string $status
     * @return mixed
     */
    public function getPermissionByStatus($status)
    {
        return $this->repository->getPermissionByStatus($status);
    }

    /**
     * Mengambil permissions dengan status aktif.
     *
     * @return mixed
     */
    public function getActivePermissions()
    {
        return Cache::remember(self::PERMISSIONS_ACTIVE_CACHE_KEY, 3600, function () {
            return $this->repository->getPermissionByStatus('Aktif');
        });
    }

    /**
     * Mengambil permissions dengan status tidak aktif.
     *
     * @return mixed
     */
    public function getInactivePermissions()
    {
        return Cache::remember(self::PERMISSIONS_INACTIVE_CACHE_KEY, 3600, function () {
            return $this->repository->getPermissionByStatus('Tidak Aktif');
        });
    }

    /**
     * Membuat permission baru.
     *
     * @param array $data
     * @return mixed
     */
    public function createPermission(array $data)
    {
        $data['guard_name'] = 'web';
        $result = $this->repository->createPermission($data);
        $this->clearPermissionCaches();
        return $result;
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
        $data['guard_name'] = 'web';
        $result = $this->repository->updatePermission($id, $data);
        $this->clearPermissionCaches();
        return $result;
    }

    /**
     * Menghapus permission berdasarkan ID.
     *
     * @param int $id
     * @return bool
     */
    public function deletePermission($id)
    {
        $result = $this->repository->deletePermission($id);
        $this->clearPermissionCaches();

        return $result;
    }

    public function updatePermissionStatus($id, $status)
    {
        $permission = $this->getPermissionById($id);

        if ($permission) {
            $result = $this->repository->updatePermissionStatus($id, $status);

            $this->clearPermissionCaches();

            return $result;
        }
    }

    /**
     * Menghapus semua cache permission
     *
     * @return void
     */
    public function clearPermissionCaches()
    {
        Cache::forget(self::PERMISSIONS_ALL_CACHE_KEY);
        Cache::forget(self::PERMISSIONS_ACTIVE_CACHE_KEY);
        Cache::forget(self::PERMISSIONS_INACTIVE_CACHE_KEY);
    }
}
