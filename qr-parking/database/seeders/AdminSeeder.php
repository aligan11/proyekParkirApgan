<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $hashedPassword = bcrypt('admin123');
        User::create([
            'first_name' => 'Khemal',
            'last_name' => 'Fasyah',
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => $hashedPassword,
            'address' => 'Jl. Hos Cokroaminoto',
            'phone' => '0895332282555',
            'profession' => 'Admin',
            'role' => 'admin',
        ]);
    }
}
