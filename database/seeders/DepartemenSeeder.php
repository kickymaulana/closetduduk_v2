<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'departemen' => 'CASTING',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'departemen' => 'SOLAR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('departemen')->insert($data);

    }
}
