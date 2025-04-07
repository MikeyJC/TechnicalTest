<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $products = ['1GB', '3GB', '5GB', '25GB', '60GB', '100GB'];

        foreach (range(1, 2000) as $i) {
            $start = $faker->dateTimeBetween('-2 years', '-1 month');
            $end = (clone $start)->modify('+1 year');

            $serviceId = DB::table('services')->insertGetId([
                'mobile_number' => $faker->unique()->numerify('07#########'),
                'network' => $faker->randomElement(['O2', 'EE', 'Plan.com']),
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('service_products')->insert([
                'service_id' => $serviceId,
                'type' => $type = $faker->randomElement($products),
                'price' => match ($type) {
                    '1GB' => 5.00,
                    '3GB' => 7.50,
                    '5GB' => 10.00,
                    '25GB' => 15.00,
                    '60GB' => 20.00,
                    '100GB' => 30.00,
                },
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
