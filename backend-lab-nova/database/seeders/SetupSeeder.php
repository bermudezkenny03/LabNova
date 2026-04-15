<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;
use App\Models\User;
use App\Models\RoleModulePermission;
use Illuminate\Database\Seeder;

class SetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create basic permissions
        $viewPerm = Permission::create([
            'name' => 'View',
            'slug' => 'view',
        ]);

        $createPerm = Permission::create([
            'name' => 'Create',
            'slug' => 'create',
        ]);

        $editPerm = Permission::create([
            'name' => 'Edit',
            'slug' => 'edit',
        ]);

        $deletePerm = Permission::create([
            'name' => 'Delete',
            'slug' => 'delete',
        ]);

        // Create Roles
        $superAdminRole = Role::create([
            'name' => 'Super Admin',
            'description' => 'Acceso total al sistema',
            'status' => true,
        ]);

        $adminRole = Role::create([
            'name' => 'Administrador',
            'description' => 'Administra usuarios, equipos, reservas y reportes',
            'status' => true,
        ]);

        $labRole = Role::create([
            'name' => 'Encargado de Laboratorio',
            'description' => 'Gestiona equipos y reservas',
            'status' => true,
        ]);

        $docenteRole = Role::create([
            'name' => 'Docente',
            'description' => 'Solicita reservas y consulta reportes',
            'status' => true,
        ]);

        $studentRole = Role::create([
            'name' => 'Estudiante',
            'description' => 'Solicita y consulta sus reservas',
            'status' => true,
        ]);

        // Create Modules
        $dashboardModule = Module::create([
            'name' => 'Dashboard',
            'slug' => 'dashboard',
            'icon' => 'fa-chart-line',
            'route' => '/dashboard',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $equipmentModule = Module::create([
            'name' => 'Equipment',
            'slug' => 'equipment',
            'icon' => 'fa-microscope',
            'route' => '/equipment',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $reservationModule = Module::create([
            'name' => 'Reservations',
            'slug' => 'reservations',
            'icon' => 'fa-calendar',
            'route' => '/reservations',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $reportsModule = Module::create([
            'name' => 'Reports',
            'slug' => 'reports',
            'icon' => 'fa-file-alt',
            'route' => '/reports',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        // Create Users
        $superAdmin = User::create([
            'name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@labnova.com',
            'password' => \Illuminate\Support\Facades\Hash::make('Password123!'),
            'phone' => '300000001',
            'status' => true,
            'role_id' => $superAdminRole->id,
        ]);

        $superAdmin->userDetail()->create([
            'gender' => 'Male',
            'birthdate' => '1990-01-01',
            'address' => 'Lab Building',
            'notes' => 'Super Administrator',
        ]);

        $admin = User::create([
            'name' => 'admin',
            'last_name' => '.',
            'email' => 'admin@labnova.com',
            'password' => \Illuminate\Support\Facades\Hash::make('Password123!'),
            'phone' => '300000002',
            'status' => true,
            'role_id' => $adminRole->id,
        ]);

        $admin->userDetail()->create([
            'gender' => 'Male',
            'birthdate' => '1992-05-15',
            'address' => 'Admin Office',
            'notes' => 'Administrator',
        ]);

        $carlos = User::create([
            'name' => 'Carlos',
            'last_name' => 'Ramirez',
            'email' => 'laboratorio@labnova.com',
            'password' => \Illuminate\Support\Facades\Hash::make('Password123!'),
            'phone' => '300000003',
            'status' => true,
            'role_id' => $labRole->id,
        ]);

        $carlos->userDetail()->create([
            'gender' => 'Male',
            'birthdate' => '1988-03-20',
            'address' => 'Lab Wing',
            'notes' => 'Laboratory Manager',
        ]);

        $laura = User::create([
            'name' => 'Laura',
            'last_name' => 'Martinez',
            'email' => 'docente@labnova.com',
            'password' => \Illuminate\Support\Facades\Hash::make('Password123!'),
            'phone' => '300000004',
            'status' => true,
            'role_id' => $docenteRole->id,
        ]);

        $laura->userDetail()->create([
            'gender' => 'Female',
            'birthdate' => '1987-07-10',
            'address' => 'Faculty Office',
            'notes' => 'Professor',
        ]);

        $juan = User::create([
            'name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'estudiante@labnova.com',
            'password' => \Illuminate\Support\Facades\Hash::make('Password123!'),
            'phone' => '300000005',
            'status' => true,
            'role_id' => $studentRole->id,
        ]);

        $juan->userDetail()->create([
            'gender' => 'Male',
            'birthdate' => '2005-11-25',
            'address' => 'Student Residence',
            'notes' => 'Student',
        ]);

        // Assign modules and permissions to Super Admin role (full access)
        foreach ([$dashboardModule, $equipmentModule, $reservationModule, $reportsModule] as $module) {
            foreach ([$viewPerm, $createPerm, $editPerm, $deletePerm] as $permission) {
                RoleModulePermission::create([
                    'role_id' => $superAdminRole->id,
                    'module_id' => $module->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }

        // Assign modules and permissions to Admin role (full access)
        foreach ([$dashboardModule, $equipmentModule, $reservationModule, $reportsModule] as $module) {
            foreach ([$viewPerm, $createPerm, $editPerm, $deletePerm] as $permission) {
                RoleModulePermission::create([
                    'role_id' => $adminRole->id,
                    'module_id' => $module->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }

        // Assign to Lab Manager (Equipment and Reservations)
        foreach ([$equipmentModule, $reservationModule] as $module) {
            foreach ([$viewPerm, $createPerm, $editPerm] as $permission) {
                RoleModulePermission::create([
                    'role_id' => $labRole->id,
                    'module_id' => $module->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }

        // Assign to Teacher (Equipment view + Reservations + Reports)
        RoleModulePermission::create([
            'role_id' => $docenteRole->id,
            'module_id' => $equipmentModule->id,
            'permission_id' => $viewPerm->id,
        ]);
        foreach ([$reservationModule, $reportsModule] as $module) {
            RoleModulePermission::create([
                'role_id' => $docenteRole->id,
                'module_id' => $module->id,
                'permission_id' => $viewPerm->id,
            ]);
            RoleModulePermission::create([
                'role_id' => $docenteRole->id,
                'module_id' => $module->id,
                'permission_id' => $createPerm->id,
            ]);
        }

        // Assign to Student (Equipment view + Reservations - limited)
        RoleModulePermission::create([
            'role_id' => $studentRole->id,
            'module_id' => $equipmentModule->id,
            'permission_id' => $viewPerm->id,
        ]);
        RoleModulePermission::create([
            'role_id' => $studentRole->id,
            'module_id' => $reservationModule->id,
            'permission_id' => $viewPerm->id,
        ]);
        RoleModulePermission::create([
            'role_id' => $studentRole->id,
            'module_id' => $reservationModule->id,
            'permission_id' => $createPerm->id,
        ]);
    }
}
