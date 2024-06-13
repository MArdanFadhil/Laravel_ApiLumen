<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::create([
        //     'username' => 'administrator',
        //     'email' => 'adminnn@gmail.com',
        //     'password' => Hash::make('admin'),
        //     'role' =>'admin',
        // ]);

        User::create([
            'username' => 'orang_staff',
            'email' => 'staff02@gmail.com',
            'password' => Hash::make('staff02'),
            'role' =>'stuff',
        ]);
    }
}

