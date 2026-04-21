<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Domain;
use App\Models\Manager;
use App\Models\Employee;
use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Status::create(['name' => 'Active']);
        Status::create(['name' => 'Expiring']);
        Status::create(['name' => 'Expire']);

        $this->call([
            BranchSeeder::class,
        ]);

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]);

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

        Domain::create([
            'name' => 'seeded-domain.com',
            'booking_date' => '2026-04-20',
            'expiry_date' => '2027-04-20',
            'sales_person_name' => 'Initial Seeder',
            'branch_id' => 1,
        ]);
    }
}
