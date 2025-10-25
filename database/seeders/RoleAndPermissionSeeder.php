<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $renamedPermissions = [
            'mengelola user' => 'mengelola users',
            'mengelola role' => 'mengelola roles',
            'mengelolan permission' => 'mengelola permissions',
        ];

        foreach ($renamedPermissions as $oldName => $newName) {
            Permission::where('name', $oldName)->update(['name' => $newName]);
        }

        // ensure guard name alignment with web
        Permission::query()->update(['guard_name' => 'web']);
        Role::query()->update(['guard_name' => 'web']);

        $permissions = [
            'mengelola users',
            'mengelola roles',
            'mengelola permissions',
            'mengelola karyawan',
            'mengelola absensi',
            'mengelola gaji',
            'mengelola cuti',
            'melakukan absensi',
            'melihat gaji',
            'melakukan cuti',
            'mencetak laporan',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission], ['guard_name' => 'web', 'status' => 'Aktif']);
        }

        $admin = Role::firstOrCreate(['name' => 'Admin'], ['guard_name' => 'web', 'status' => 'Aktif']);
        $admin->syncPermissions([
            'mengelola users',
            'mengelola roles',
            'mengelola permissions',
            'mengelola karyawan',
            'mengelola absensi',
            'mengelola gaji',
            'mengelola cuti',
            'mencetak laporan',
        ]);

        $owner = Role::firstOrCreate(['name' => 'Owner'], ['guard_name' => 'web', 'status' => 'Aktif']);
        $owner->syncPermissions([
            'mengelola users',
            'mengelola gaji',
            'mengelola cuti',
            'mengelola absensi',
            'mencetak laporan',
        ]);

        $karyawan = Role::firstOrCreate(['name' => 'Karyawan'], ['guard_name' => 'web', 'status' => 'Aktif']);
        $karyawan->syncPermissions([
            'melakukan absensi',
            'melihat gaji',
            'melakukan cuti',
        ]);
    }
}
