<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('guest')->insert([
            [
                'uuid' => Str::uuid(),
                'name' => 'Nguyen Van A',
                'contact_details' => '0901234567',
                'id_number' => '123456789',
                'passport_number' => 'AB1234567',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 'admin',
                'updated_by' => 'admin',
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Tran Thi B',
                'contact_details' => '0912345678',
                'id_number' => '987654321',
                'passport_number' => 'CD7654321',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 'admin',
                'updated_by' => 'admin',
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Le Van C',
                'contact_details' => '0923456789',
                'id_number' => '112233445',
                'passport_number' => 'EF1237890',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 'admin',
                'updated_by' => 'admin',
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Pham Thi D',
                'contact_details' => '0934567890',
                'id_number' => '554433221',
                'passport_number' => 'GH9876543',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 'admin',
                'updated_by' => 'admin',
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Vu Van E',
                'contact_details' => '0945678901',
                'id_number' => '667788990',
                'passport_number' => 'IJ7654321',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 'admin',
                'updated_by' => 'admin',
            ],
        ]);
    }
}
