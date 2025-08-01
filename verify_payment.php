<?php
require('vendor/autoload.php');

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

// Replace these with your actual Razorpay Key ID and Secret
$keyId = 'YOUR_KEY_ID';
$keySecret = 'YOUR_KEY_SECRET';

$api = new Api($keyId, $keySecret);

// Make sure the payment form sends these values
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = $_POST['razorpay_payment_id'] ?? '';
    $order_id   = $_POST['razorpay_order_id'] ?? '';
    $signature  = $_POST['razorpay_signature'] ?? '';

    try {
        $attributes = [
            'razorpay_order_id' => $order_id,
            'razorpay_payment_id' => $payment_id,
            'razorpay_signature' => $signature
        ];

        $api->utility->verifyPaymentSignature($attributes);

        // Fetch payment details (optional)
        $payment = $api->payment->fetch($payment_id);

        // Store payment info in DB here (recommended)
        echo "<h3 style='color:green;'>Payment successful!</h3>";
        echo "<p>Payment ID: " . htmlspecialchars($payment_id) . "</p>";
        echo "<p>Order ID: " . htmlspecialchars($order_id) . "</p>";

    } catch (SignatureVerificationError $e) {
        echo "<h3 style='color:red;'>Payment failed: Signature verification failed</h3>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    } catch (Exception $e) {
        echo "<h3 style='color:red;'>Something went wrong</h3>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<h3 style='color:red;'>Invalid request</h3>";
}
?>
