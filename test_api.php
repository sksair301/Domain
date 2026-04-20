<?php

$url = 'http://127.0.0.1:8001/api/domains';
$data = [
    'name' => 'example.com',
    'booking_date' => '2026-04-20',
    'expiry_date' => '2027-04-20',
    'branch' => 'Main Branch'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

// Now test GET
$response = file_get_contents($url);
echo "\nGET Response:\n$response\n";
