<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Email Preview Routes (for development only)
Route::get('/preview/domain-created', function () {
    $domain = App\Models\Domain::first();
    return new App\Mail\DomainCreatedMail($domain);
});

Route::get('/preview/domain-expiry/{days?}', function ($days = 7) {
    $domain = App\Models\Domain::first();
    return new App\Mail\DomainExpiryMail($domain, (int)$days);
});

Route::get('/preview/payment-added', function () {
    $payment = App\Models\Payment::with('domain')->first();
    return new App\Mail\PaymentAddedMail($payment);
});
