<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Driver;
use App\Models\Member;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class  UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::factory()->create([
            'name' => 'Manager',
            'email' => 'manager@admin.com',
            'password' => '12345678',
        ]);
        $manager->assignRole('manager');

        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@admin.com',
            'password' => '12345678',
        ]);
        $user->assignRole('user');


    }
}
