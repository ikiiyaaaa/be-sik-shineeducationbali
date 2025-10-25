<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a Owner user using the User factory
        $owner = User::factory()->create([
            'name'  => 'Owner',
            'email' => 'owner@example.com',
        ]);

        // Assign the Owner role
        $owner->assignRole('Owner');

        // Create a admin user using the User factory
        $admin = User::factory()->create([
            'name'  => 'Admin',
            'email' => 'admin@example.com',
        ]);

        // Assign the Admin role
        $admin->assignRole('Admin');

        // Create a Karyawan user using the User factory
        $karyawan = User::factory()->create([
            'name'  => 'Karyawan',
            'email' => 'karyawan@example.com',
        ]);

        // Assign the Karyawan role
        $karyawan->assignRole('Karyawan');
    }
}
