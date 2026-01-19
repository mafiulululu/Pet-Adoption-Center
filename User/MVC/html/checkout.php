<?php
session_start();
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart)) {
    header("Location: pet_food.php");
    exit();
}

$total_price = 0;
foreach ($cart as $item) {
    $total_price += $item['price'] * $item['quantity'];
}
$tax = $total_price * 0.05;
$final_total = $total_price + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Pet Adoption Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/home.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .checkout-container {
            max-width: 1000px;
            margin: 3rem auto;
            padding: 0 1.5rem;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 2rem;
        }
        .checkout-section {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            border: 1px solid #e5e7eb;
        }
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #111827;
            text-align: left;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            color: #4b5563;
        }
        .summary-total {
            border-top: 2px solid #e5e7eb;
            padding-top: 1rem;
            margin-top: 1rem;
            font-weight: 700;
            font-size: 1.2rem;
            color: #111827;
            display: flex;
            justify-content: space-between;
        }
        .btn-pay {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #FF1B6B 0%, #45CAFF 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: transform 0.2s;
        }
        .btn-pay:hover {
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

   
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <span class="brand-name">Pet Adoption Center</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="cart.php" class="nav-link">← Back to Cart</a></li>
            </ul>
        </div>
    </nav>

    <div class="container checkout-container">
        
       
        <div class="checkout-section">
            <h2 class="section-title">Shipping Details</h2>
            <form action="process_payment.php" method="POST" id="paymentForm">
                <div class="input-group" style="margin-bottom: 1rem;">
                    <label class="input-label">Full Name</label>
                    <input type="text" name="fullname" class="input-field" required placeholder="Enter your full name">
                </div>
                
                <div class="input-group" style="margin-bottom: 1rem;">
                    <label class="input-label">Email Address</label>
                    <input type="email" name="email" class="input-field" required placeholder="Enter your email address ">
                </div>

                <div class="input-group" style="margin-bottom: 1rem;">
                    <label class="input-label">Address</label>
                    <input type="text" name="address" class="input-field" required placeholder=" ">
                </div>

                <div class="form-row">
                    <div class="input-group" style="margin-bottom: 1rem;">
                        <label class="input-label">City</label>
                        <input type="text" name="city" class="input-field" required>
                    </div>
                    
                </div>

                <h2 class="section-title" style="margin-top: 2rem;">Payment Information</h2>
                
                <div class="input-group" style="margin-bottom: 1rem;">
                    <label class="input-label">Card Number</label>
                    <input type="text" name="card_number" class="input-field" required placeholder="0000 0000 0000 0000" maxlength="19">
                </div>

                <div class="form-row">
                    <div class="input-group" style="margin-bottom: 1rem;">
                        <label class="input-label">Expiry Date</label>
                        <input type="text" name="expiry" class="input-field" required placeholder="MM/YY" maxlength="5">
                    </div>
                    <div class="input-group" style="margin-bottom: 1rem;">
                        <label class="input-label">CVV</label>
                        <input type="text" name="cvv" class="input-field" required placeholder="123" maxlength="3">
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Column: Summary -->
        <div class="checkout-section" style="height: fit-content;">
            <h2 class="section-title">Order Summary</h2>
            
            <?php foreach ($cart as $item): ?>
            <div class="summary-item">
                <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </div>
            <?php endforeach; ?>

            <div style="border-top: 1px solid #eee; margin: 1rem 0;"></div>

            <div class="summary-item">
                <span>Subtotal</span>
                <span>$<?php echo number_format($total_price, 2); ?></span>
            </div>
            <div class="summary-item">
                <span>Tax (5%)</span>
                <span>$<?php echo number_format($tax, 2); ?></span>
            </div>
            
            <div class="summary-total">
                <span>Total</span>
                <span>$<?php echo number_format($final_total, 2); ?></span>
            </div>

            <button type="submit" form="paymentForm" class="btn-pay">Pay $<?php echo number_format($final_total, 2); ?></button>
            
            <div style="text-align: center; margin-top: 1rem; font-size: 0.9rem; color: #6b7280;">
                🔒 Secure SSL Encryption
            </div>
        </div>

    </div>

</body>
</html>