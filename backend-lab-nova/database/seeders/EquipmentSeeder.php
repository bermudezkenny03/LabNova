<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Equipment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $computers = Category::where('name', 'Computadores')->first();
        $projectors = Category::where('name', 'Proyectores')->first();
        $kits = Category::where('name', 'Kits Electrónicos')->first();
        $instruments = Category::where('name', 'Instrumentos de Medición')->first();

        $equipmentList = [
            [
                'category_id' => $computers?->id,
                'name' => 'Portátil Dell Latitude 5420',
                'code' => 'EQ-001',
                'description' => 'Portátil para prácticas de programación',
                'stock' => 10,
                'status' => 'available',
                'is_active' => 1,
            ],
            [
                'category_id' => $computers?->id,
                'name' => 'Portátil HP ProBook 440',
                'code' => 'EQ-002',
                'description' => 'Equipo portátil para uso académico',
                'stock' => 8,
                'status' => 'available',
                'is_active' => 1,
            ],
            [
                'category_id' => $projectors?->id,
                'name' => 'Video Beam Epson X49',
                'code' => 'EQ-003',
                'description' => 'Proyector multimedia para presentaciones',
                'stock' => 4,
                'status' => 'available',
                'is_active' => 1,
            ],
            [
                'category_id' => $kits?->id,
                'name' => 'Kit Arduino Uno',
                'code' => 'EQ-004',
                'description' => 'Kit básico de prototipado con Arduino',
                'stock' => 15,
                'status' => 'available',
                'is_active' => 1,
            ],
            [
                'category_id' => $instruments?->id,
                'name' => 'Multímetro Digital Uni-T',
                'code' => 'EQ-005',
                'description' => 'Instrumento para medición eléctrica',
                'stock' => 6,
                'status' => 'maintenance',
                'is_active' => 1,
            ],
        ];

        foreach ($equipmentList as $equipment) {
            Equipment::create($equipment);
        }

        $this->command->info('✅ Equipos creados correctamente');
    }
}