<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Manager;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            // branch_id is nullable or doesn't apply to superadmin
        ]);

        // Create Manager
        $managerUser = User::create([
            'name' => 'Ahmedabad Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'branch_id' => 1,
        ]);

        Manager::create([
            'user_id' => $managerUser->id,
            'name' => $managerUser->name,
            'email' => $managerUser->email,
            'phone_number' => '1234567890',
            'branch_id' => 1,
        ]);

        // Create Employee
        $employeeUser = User::create([
            'name' => 'Ahmedabad Employee',
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'branch_id' => 1,
        ]);

        Employee::create([
            'user_id' => $employeeUser->id,
            'name' => $employeeUser->name,
            'email' => $employeeUser->email,
            'phone_number' => '0987654321',
            'branch_id' => 1,
        ]);
    }
}
