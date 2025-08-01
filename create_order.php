<?php
require('vendor/autoload.php');

use Razorpay\Api\Api;

$keyId = 'YOUR_KEY_ID';
$keySecret = 'YOUR_KEY_SECRET';

$api = new Api($keyId, $keySecret);

$orderData = [
    'receipt'         => 'rcptid_' . rand(1000, 9999),
    'amount'          => 10000, // ₹100 in paise
    'currency'        => 'INR',
    'payment_capture' => 1
];

$order = $api->order->create($orderData);
echo json_encode($order->toArray());
