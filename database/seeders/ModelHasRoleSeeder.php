<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelHasRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $assignments = [
            ['role_id' => 1, 'model_id' => 1],
        ];

        foreach ($assignments as $assign) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id'    => $assign['role_id'],
                    'model_id'   => $assign['model_id'],
                    'model_type' => 'App\Models\User'
                ],
                [] // Tidak ada kolom tambahan yang perlu diupdate
            );
        }
    }
}
