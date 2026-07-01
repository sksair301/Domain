<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Domain;
use App\Models\Payment;
use Carbon\Carbon;

echo "1. Creating a new payment to trigger PaymentAddedMail...\n";
$domain = Domain::first();
if ($domain) {
    Payment::create([
        'domain_id' => $domain->id,
        'amount' => 500.00,
        'payment_date' => Carbon::now()->format('Y-m-d'),
        'payment_status_id' => 1,
    ]);
    echo "Payment created successfully. (Email dispatch triggered)\n";
} else {
    echo "No domains found to create payment for.\n";
}

echo "\n2. Triggering Domain Expiry Notification...\n";
// Update a domain to expire exactly 7 days from now
if ($domain) {
    $domain->expiry_date = Carbon::now()->addDays(7)->format('Y-m-d');
    $domain->save();
    echo "Updated domain '{$domain->name}' to expire in 7 days.\n";
    
    // Call the artisan command
    \Illuminate\Support\Facades\Artisan::call('app:send-domain-expiry-notifications');
    echo \Illuminate\Support\Facades\Artisan::output();
}
