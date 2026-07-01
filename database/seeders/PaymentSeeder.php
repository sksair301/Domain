<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Domain;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domain = Domain::first();
        
        if ($domain) {
            Payment::create([
                'domain_id' => $domain->id,
                'amount' => 1500.00,
                'payment_date' => '2026-04-20',
                'payment_status_id' => 1, // Assuming 1 is 'Paid' or 'Received'
            ]);
        }
    }
}
