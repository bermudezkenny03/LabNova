<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Administrador')->first();
        $labManagerRole = Role::where('name', 'Encargado de Laboratorio')->first();
        $teacherRole = Role::where('name', 'Docente')->first();
        $studentRole = Role::where('name', 'Estudiante')->first();

        if ($superAdminRole) {
            User::create([
                'name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@labnova.com',
                'password' => Hash::make('Password123!'),
                'phone' => '300000001',
                'status' => 1,
                'role_id' => $superAdminRole->id,
            ]);
        }

        if ($adminRole) {
            User::create([
                'name' => 'Ana',
                'last_name' => 'Torres',
                'email' => 'admin@labnova.com',
                'password' => Hash::make('Password123!'),
                'phone' => '300000002',
                'status' => 1,
                'role_id' => $adminRole->id,
            ]);
        }

        if ($labManagerRole) {
            User::create([
                'name' => 'Carlos',
                'last_name' => 'Ramirez',
                'email' => 'laboratorio@labnova.com',
                'password' => Hash::make('Password123!'),
                'phone' => '300000003',
                'status' => 1,
                'role_id' => $labManagerRole->id,
            ]);
        }

        if ($teacherRole) {
            User::create([
                'name' => 'Laura',
                'last_name' => 'Martinez',
                'email' => 'docente@labnova.com',
                'password' => Hash::make('Password123!'),
                'phone' => '300000004',
                'status' => 1,
                'role_id' => $teacherRole->id,
            ]);
        }

        if ($studentRole) {
            User::create([
                'name' => 'Juan',
                'last_name' => 'Perez',
                'email' => 'estudiante@labnova.com',
                'password' => Hash::make('Password123!'),
                'phone' => '300000005',
                'status' => 1,
                'role_id' => $studentRole->id,
            ]);
        }

        $this->command->info('✅ Usuarios creados correctamente');
    }
}