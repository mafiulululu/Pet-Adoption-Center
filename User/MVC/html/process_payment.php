<?php
session_start();
// Simulate payment processing...
sleep(1);

// Clear the cart after payment
unset($_SESSION['cart']);

// Redirect with success
echo '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Payment Success</title><link rel="stylesheet" href="../css/style.css"><meta http-equiv="refresh" content="3;url=home.php"></head>
<body><div class="container" style="text-align: center; padding-top: 5rem;">
    <div style="background: #d1fae5; color: #065f46; padding: 2rem; border-radius: 12px; display: inline-block;">
        <h2>✅ Payment Successful!</h2><p>Thank you for your purchase.</p><p>Redirecting to Home...</p>
    </div></div></body></html>';
?>