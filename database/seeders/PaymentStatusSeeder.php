<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Not Received',
            'Partially Received',
            'Received',
        ];

        foreach ($statuses as $status) {
            \App\Models\PaymentStatus::updateOrCreate(['name' => $status]);
        }
    }
}
