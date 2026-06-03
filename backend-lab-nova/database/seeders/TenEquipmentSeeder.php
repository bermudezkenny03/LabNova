<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\EquipmentStatus;
use Illuminate\Database\Seeder;

class TenEquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $statusAvailable = EquipmentStatus::where('code', 'available')->first()?->id;
        if (!$statusAvailable) {
            $this->command->error('Estado de equipo "available" no encontrado.');
            return;
        }

        $categories = Category::whereIn('name', [
            'Computadores',
            'Proyectores',
            'Kits Electrónicos',
            'Instrumentos de Medición',
        ])->get()->keyBy('name');

        $items = [
            ['category' => 'Computadores', 'name' => 'Laptop Dell Inspiron 15', 'description' => 'Laptop para clases de programación y ofimática.', 'stock' => 6],
            ['category' => 'Computadores', 'name' => 'Desktop HP Pavilion', 'description' => 'Equipo de escritorio para prácticas de laboratorio.', 'stock' => 4],
            ['category' => 'Computadores', 'name' => 'Laptop Lenovo ThinkPad E14', 'description' => 'Equipo portátil para desarrollo y diseño.', 'stock' => 5],
            ['category' => 'Proyectores', 'name' => 'Proyector Epson EB-X41', 'description' => 'Proyector para presentaciones y clases.', 'stock' => 3],
            ['category' => 'Proyectores', 'name' => 'Proyector ViewSonic PA503W', 'description' => 'Proyector de alta luminosidad para aulas.', 'stock' => 2],
            ['category' => 'Kits Electrónicos', 'name' => 'Kit Arduino Starter', 'description' => 'Kit básico de electrónica con Arduino.', 'stock' => 12],
            ['category' => 'Kits Electrónicos', 'name' => 'Kit Raspberry Pi Essentials', 'description' => 'Kit completo para proyectos con Raspberry Pi.', 'stock' => 8],
            ['category' => 'Instrumentos de Medición', 'name' => 'Multímetro Digital Fluke', 'description' => 'Multímetro para mediciones de voltaje, corriente y resistencia.', 'stock' => 7],
            ['category' => 'Instrumentos de Medición', 'name' => 'Osciloscopio Rigol DS1102E', 'description' => 'Osciloscopio para análisis de señales eléctricas.', 'stock' => 2],
            ['category' => 'Kits Electrónicos', 'name' => 'Kit de Sensores IoT', 'description' => 'Kit de sensores para prácticas de Internet de las Cosas.', 'stock' => 10],
        ];

        foreach ($items as $index => $item) {
            $category = $categories->get($item['category']);
            if (!$category) {
                $this->command->error('Categoría no encontrada: ' . $item['category']);
                continue;
            }

            Equipment::create([
                'category_id' => $category->id,
                'name' => $item['name'],
                'code' => sprintf('TEN-%02d', $index + 1),
                'description' => $item['description'],
                'stock' => $item['stock'],
                'equipment_status_id' => $statusAvailable,
                'is_active' => 1,
            ]);
        }

        $this->command->info('✅ Se han insertado 10 equipos nuevos.');
    }
}
