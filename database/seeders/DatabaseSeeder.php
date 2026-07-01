<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            StatusSeeder::class,
            BranchSeeder::class,
            PaymentStatusSeeder::class,
            UserSeeder::class,
            DomainSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
