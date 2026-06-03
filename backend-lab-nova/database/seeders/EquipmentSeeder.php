<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\EquipmentStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Equipment::truncate();
        Schema::enableForeignKeyConstraints();

        $computers = Category::where('name', 'Computadores')->first();
        $projectors = Category::where('name', 'Proyectores')->first();
        $kits = Category::where('name', 'Kits Electrónicos')->first();
        $instruments = Category::where('name', 'Instrumentos de Medición')->first();

        // Obtener IDs de estados
        $statusAvailable = EquipmentStatus::where('code', 'available')->first()?->id;
        $statusMaintenance = EquipmentStatus::where('code', 'maintenance')->first()?->id;

        $equipmentData = [
            'Computadores' => [
                ['name' => 'Portátil Dell Latitude 5420', 'description' => 'Portátil para prácticas de programación', 'stock' => 10],
                ['name' => 'Portátil HP ProBook 440', 'description' => 'Equipo portátil para uso académico', 'stock' => 8],
                ['name' => 'Portátil Acer Aspire 5', 'description' => 'Portátil ligero para desarrollo y tareas generales', 'stock' => 12],
                ['name' => 'PC de Escritorio Lenovo ThinkCentre M75s', 'description' => 'Equipo de escritorio para prácticas de laboratorio', 'stock' => 6],
                ['name' => 'Portátil ASUS VivoBook 15', 'description' => 'Portátil para uso cotidiano y pruebas', 'stock' => 9],
                ['name' => 'PC de Escritorio HP EliteDesk 800', 'description' => 'Equipo de alto rendimiento para diseño y simulación', 'stock' => 5],
                ['name' => 'Portátil Apple MacBook Air', 'description' => 'Portátil para diseño y desarrollo macOS', 'stock' => 4],
                ['name' => 'Portátil Lenovo IdeaPad 3', 'description' => 'Equipo de entrada para estudiantes', 'stock' => 11],
                ['name' => 'Portátil MSI Modern 14', 'description' => 'Portátil para trabajos multimedia', 'stock' => 7],
                ['name' => 'PC de Escritorio Dell OptiPlex 3080', 'description' => 'Equipo de escritorio para laboratorio de informática', 'stock' => 6],
                ['name' => 'Portátil Samsung Galaxy Book', 'description' => 'Portátil híbrido para clases y tareas', 'stock' => 5],
                ['name' => 'Portátil Chuwi HeroBook', 'description' => 'Equipo económico para práctica de software', 'stock' => 8],
                ['name' => 'PC de Escritorio Acer Veriton', 'description' => 'Equipo de escritorio para servidores y pruebas', 'stock' => 5],
                ['name' => 'Portátil Toshiba Tecra A50', 'description' => 'Portátil resistente para uso académico', 'stock' => 4],
                ['name' => 'Portátil Huawei MateBook D', 'description' => 'Portátil con buena autonomía para estudiantes', 'stock' => 6],
            ],
            'Proyectores' => [
                ['name' => 'Video Beam Epson X49', 'description' => 'Proyector multimedia para presentaciones', 'stock' => 4],
                ['name' => 'Proyector BenQ MX528', 'description' => 'Proyector para aulas y exposiciones', 'stock' => 3],
                ['name' => 'Proyector Sony VPL-DX221', 'description' => 'Proyector de alta definición para clases', 'stock' => 2],
                ['name' => 'Proyector Optoma HD146X', 'description' => 'Proyector de cine y multimedia', 'stock' => 3],
                ['name' => 'Proyector LG PF50KA', 'description' => 'Proyector portátil para presentaciones', 'stock' => 2],
                ['name' => 'Proyector ViewSonic PA503S', 'description' => 'Proyector de larga duración para aulas', 'stock' => 3],
                ['name' => 'Proyector NEC NP-ME401W', 'description' => 'Proyector de alta luminosidad', 'stock' => 2],
                ['name' => 'Proyector Acer H6517ST', 'description' => 'Proyector de corto alcance para aulas pequeñas', 'stock' => 2],
                ['name' => 'Proyector Philips PicoPix Max', 'description' => 'Proyector portátil compacto', 'stock' => 3],
                ['name' => 'Proyector Xiaomi Mi Smart Laser', 'description' => 'Proyector láser inteligente para demostraciones', 'stock' => 1],
            ],
            'Kits Electrónicos' => [
                ['name' => 'Kit Arduino Uno', 'description' => 'Kit básico de prototipado con Arduino', 'stock' => 15],
                ['name' => 'Kit de Sensores Arduino', 'description' => 'Kit de sensores y módulos para Arduino', 'stock' => 12],
                ['name' => 'Kit de Robótica Makeblock', 'description' => 'Kit de robótica para ensamblaje educativo', 'stock' => 10],
                ['name' => 'Kit Raspberry Pi 4', 'description' => 'Kit completo con Raspberry Pi y accesorios', 'stock' => 8],
                ['name' => 'Kit de Electrónica Básica', 'description' => 'Componentes básicos para circuitos simples', 'stock' => 18],
                ['name' => 'Kit de Circuitos Digitales', 'description' => 'Kit para prácticas de lógica digital', 'stock' => 10],
                ['name' => 'Kit de Microcontroladores STM32', 'description' => 'Kit avanzado para microcontroladores', 'stock' => 6],
                ['name' => 'Kit de Domótica', 'description' => 'Kit para prácticas de automatización del hogar', 'stock' => 7],
                ['name' => 'Kit de FPGA', 'description' => 'Kit para diseño de circuitos programables', 'stock' => 5],
                ['name' => 'Kit de Montaje de Drones', 'description' => 'Kit educativo para construcción de drones', 'stock' => 4],
                ['name' => 'Kit de Prototipado con Breadboard', 'description' => 'Kit de experimentos y prototipos electrónicos', 'stock' => 16],
                ['name' => 'Kit de Componentes SMD', 'description' => 'Kit surtido de componentes SMD y herramientas', 'stock' => 9],
                ['name' => 'Kit de Programación IoT', 'description' => 'Kit para proyectos de Internet de las Cosas', 'stock' => 6],
            ],
            'Instrumentos de Medición' => [
                ['name' => 'Multímetro Digital Uni-T', 'description' => 'Instrumento para medición eléctrica', 'stock' => 6],
                ['name' => 'Osciloscopio Rigol DS1054Z', 'description' => 'Osciloscopio digital para análisis de señales', 'stock' => 3],
                ['name' => 'Generador de Señales', 'description' => 'Generador de ondas para pruebas de circuitos', 'stock' => 4],
                ['name' => 'Fuente de Poder Regulada', 'description' => 'Fuente de alimentación estable para experimentos', 'stock' => 5],
                ['name' => 'Analizador Lógico', 'description' => 'Herramienta para diagnóstico de señales digitales', 'stock' => 4],
                ['name' => 'Calibrador de Multímetros', 'description' => 'Equipo de calibración para instrumentos de medición', 'stock' => 2],
                ['name' => 'Termómetro Infrarrojo', 'description' => 'Medición de temperatura sin contacto', 'stock' => 8],
                ['name' => 'Medidor de Potencia', 'description' => 'Medidor para análisis de energía eléctrica', 'stock' => 3],
                ['name' => 'Tacómetro Digital', 'description' => 'Medidor de velocidad de rotación', 'stock' => 4],
                ['name' => 'Detector de Metal', 'description' => 'Detector portátil para búsqueda de objetos metálicos', 'stock' => 5],
                ['name' => 'Microscopio Digital', 'description' => 'Microscopio para observación de muestras', 'stock' => 4],
                ['name' => 'Medidor de Temperatura y Humedad', 'description' => 'Instrumento ambiental multiusos', 'stock' => 6],
            ],
        ];

        $equipmentList = [];
        $codeIndex = 1;

        foreach ($equipmentData as $categoryName => $items) {
            $category = Category::where('name', $categoryName)->first();
            foreach ($items as $item) {
                $equipmentList[] = [
                    'category_id' => $category?->id,
                    'name' => $item['name'],
                    'code' => sprintf('EQ-%03d', $codeIndex),
                    'description' => $item['description'],
                    'stock' => $item['stock'],
                    'equipment_status_id' => ($codeIndex % 7 === 0) ? $statusMaintenance : $statusAvailable,
                    'is_active' => 1,
                ];
                $codeIndex++;
            }
        }

        foreach ($equipmentList as $equipment) {
            Equipment::create($equipment);
        }

        $this->command->info('✅ Equipos creados correctamente');
    }
}
