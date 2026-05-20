<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Category::truncate();
        Schema::enableForeignKeyConstraints();

        $categories = [
            [
                'name' => 'Computadores',
                'description' => 'Equipos de cómputo para prácticas de laboratorio',
            ],
            [
                'name' => 'Proyectores',
                'description' => 'Equipos de proyección para clases y exposiciones',
            ],
            [
                'name' => 'Kits Electrónicos',
                'description' => 'Componentes y kits para prácticas de electrónica',
            ],
            [
                'name' => 'Instrumentos de Medición',
                'description' => 'Multímetros, osciloscopios y herramientas de medición',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'status' => 1,
            ]);
        }

        $this->command->info('✅ Categorías creadas correctamente');
    }
}