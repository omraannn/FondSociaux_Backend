<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'RH']);
        User::create([
            'firstname' => 'Admin',
            'lastname' => 'Admin',
            'email' => 'admin@admin.com', // email : admin@admin.com
            'password' => Hash::make('password'), // password : password
            'age' => 30,
            'cin' => 'C1234567',
            'front_image' => null,
            'back_image' => null,
            'address' => '123 Admin St',
            'tel' => '28085788',
            'status' => 1,
            'email_verified_at' => now(),
        ])->assignRole($adminRole);
    }
}
