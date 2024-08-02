<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Tasa de cambio o factor de conversión
        $tasaCambio = 4000; // Puedes ajustar según la tasa de cambio actual

        // Genera 50 registros aleatorios para la tabla de productos
        for ($i = 0; $i < 50; $i++) {
            $precioDolares = $faker->randomFloat(2, 5, 100);
            $precioPesos = $precioDolares * $tasaCambio;

            DB::table('productos')->insert([
                "nombre" => $faker->word,
                "codigo" => $faker->numberBetween(1000, 999),
                "precio" => $precioPesos,
                "stock" => $faker->numberBetween(10, 1000),
                "categoria" => $faker->randomElement(['Frutas', 'Verduras']),
                "proveedor" => $faker->company,
                "impuesto" => $faker->randomFloat(2, 0, 0.2),
                "unidad_medida" => $faker->randomElement(['Kg', 'Unidad']),
                "imagen" => $faker->imageUrl(200, 200),
            ]);
        }
    }

    public function down()
    {
        // Elimina los registros insertados durante el seeding
        DB::table('productos')->refresh();
    }
}
