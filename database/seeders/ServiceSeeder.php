<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 5; $i++) {
            DB::table('service')->insert([
                'uuid' => Str::uuid(),
                'service_name' => 'Service ' . ($i + 1),
                'service_price' => rand(100, 500),
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 'admin',
                'updated_by' => 'admin',
            ]);
        }
    }
}
