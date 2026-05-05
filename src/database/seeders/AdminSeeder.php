<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
