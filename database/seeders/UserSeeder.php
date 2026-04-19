<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Perhatikan penambahan kurung siku [ di awal dan ] di akhir daftar
        $users = [
            ['id' => 1, 'username' => 'admin', 'name' => 'Admin'],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['username' => $user['username']],
                [
                    'name' => $user['name'],
                    'email' => strtolower($user['username']) . '@perusahaan.com',
                    'email_verified_at' => Carbon::now(),
                    'password' => Hash::make('password123'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
