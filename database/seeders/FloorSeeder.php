<?php

namespace Database\Seeders;

use App\Models\Floor;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guestIds = DB::table('guest')->pluck('id')->toArray();

        for ($i = 0; $i < 5; $i++) {
            DB::table('floor')->insert([
                'uuid' => Str::uuid(),
                'hotel_id' => $guestIds[array_rand($guestIds)],
                'floor_number' => $i,

                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 'admin',
                'updated_by' => 'admin',
            ]);
        }
    }
}
