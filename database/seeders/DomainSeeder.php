<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Domain;
use App\Models\User;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find a sales person (employee)
        $salesPerson = User::where('role', 'employee')->first();
        $salesPersonId = $salesPerson ? $salesPerson->id : null;

        Domain::create([
            'name' => 'seeded-domain.com',
            'booking_date' => '2026-04-20',
            'expiry_date' => '2027-04-20',
            'total_amount' => 1500.00,
            'sales_person_id' => $salesPersonId,
            'system_status' => 'Active',
            'branch_id' => 1,
            'remark' => 'Created via seeder',
        ]);
        
        Domain::create([
            'name' => 'expiring-domain.com',
            'booking_date' => '2025-07-10',
            'expiry_date' => \Carbon\Carbon::now()->addDays(5)->format('Y-m-d'),
            'total_amount' => 1200.00,
            'sales_person_id' => $salesPersonId,
            'system_status' => 'Expiring soon',
            'branch_id' => 1,
            'remark' => 'Expiring soon domain via seeder',
        ]);
    }
}
