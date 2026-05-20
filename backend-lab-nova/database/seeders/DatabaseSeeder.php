<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            GenderSeeder::class,
            EquipmentStatusSeeder::class,
            ReservationStatusSeeder::class,
            ReservationLogActionSeeder::class,
            ReportStatusSeeder::class,
            ReportTypeSeeder::class,
            PermissionSeeder::class,
            CategorySeeder::class,
            EquipmentSeeder::class,
            UserSeeder::class,
        ]);
    }
}
