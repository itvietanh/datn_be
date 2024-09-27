<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guestIds = DB::table('guest')->pluck('id')->toArray();

        for ($i = 0; $i < 5; $i++) {
            DB::table('transition')->insert([
                'uuid' => Str::uuid(),
                'guest_id' => $guestIds[array_rand($guestIds)],
                'transition_date' => now(),
                'payment_status' => rand(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 'admin',
                'updated_by' => 'admin',
            ]);
        }
    }
}
