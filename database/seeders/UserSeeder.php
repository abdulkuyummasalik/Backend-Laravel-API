<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Zegen',
            'email' => 'eve.holt@reqres.in',
            'password' => Hash::make('zegen123')
        ]);
    }
}
