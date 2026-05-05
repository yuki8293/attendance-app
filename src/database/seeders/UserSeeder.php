<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'test user',
                'password' => Hash::make('password'),
            ]
        );
    }
}
