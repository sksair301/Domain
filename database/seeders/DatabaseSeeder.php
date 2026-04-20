<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Domain;
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
        $this->call([
            BranchSeeder::class,
        ]);

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]);

        User::create([
            'name' => 'Ahmedabad Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'branch_id' => 1,
        ]);

        User::create([
            'name' => 'Ahmedabad Employee',
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
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
