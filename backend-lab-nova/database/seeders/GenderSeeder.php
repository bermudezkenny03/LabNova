<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder: Poblar tabla de géneros
 *
 * Inserta los géneros predeterminados:
 * - Masculino (Male)
 * - Femenino (Female)
 * - Otro (Other)
 * - Prefiero no responder (Prefer not to say)
 *
 * @see \App\Models\Gender
 */
class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('genders')->insertOrIgnore([
            [
                'name' => 'Masculino',
                'code' => 'male',
                'status' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Femenino',
                'code' => 'female',
                'status' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Otro',
                'code' => 'other',
                'status' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Prefiero no responder',
                'code' => 'prefer_not_to_say',
                'status' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
