<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert roles
        DB::table('tbrole')->insertOrIgnore([
            [
                'role_id' => 1,
                'role_type' => 'Administrator',
                'permissions_list' => 'admin_global,manage_all',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => 2,
                'role_type' => 'Manager',
                'permissions_list' => 'admin_local,manage_local',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert admin user
        DB::table('tbuser')->insertOrIgnore([
            [
                'user_id' => 1,
                'full_name' => 'Administrator Principal',
                'phone' => '8888-0000',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'full_name' => 'Gerente Punta Mona',
                'phone' => '8888-0001',
                'email' => 'gerente.puntamona@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'full_name' => 'Gerente El Sevichito',
                'phone' => '8888-0002',
                'email' => 'gerente.sevichito@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Assign locals to managers
        DB::table('tbuser_local')->insertOrIgnore([
            [
                'user_id' => 2,
                'local_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'local_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
