<?php
session_start();
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - Pet Adoption Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Navigation Header -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <span class="brand-name">Pet Adoption Center</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="home.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="pet_food.php" class="nav-link">Shop</a></li>
            </ul>
        </div>
    </nav>

    <div class="container cart-container">
        <div class="page-header">
            <h1 class="page-title">Shopping Cart 🛒</h1>
            <a href="pet_food.php" class="btn-back-shop">← Back to Shop</a>
        </div>

        <?php if (empty($cart)): ?>
            <div class="empty-cart">
                <div class="empty-icon">🛍️</div>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added any treats or toys yet.</p>
                <a href="pet_food.php" class="btn-primary" style="display:inline-block; width:auto; margin-top:1rem;">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-grid">
                <div class="cart-items">
                    <?php foreach ($cart as $key => $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total_price += $subtotal;
                    ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                        
                        <div class="item-details">
                            <h3 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="item-price">$<?php echo number_format($item['price'], 2); ?></p>
                        </div>

                        <div class="item-actions">
                            <form action="cart_action.php" method="POST" class="qty-form">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="key" value="<?php echo $key; ?>">
                                <button type="button" onclick="this.nextElementSibling.stepDown(); this.form.submit()" class="qty-btn">-</button>
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="qty-input" readonly>
                                <button type="button" onclick="this.previousElementSibling.stepUp(); this.form.submit()" class="qty-btn">+</button>
                            </form>
                            
                            <form action="cart_action.php" method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="key" value="<?php echo $key; ?>">
                                <button type="submit" class="btn-remove">Remove</button>
                            </form>
                        </div>
                        
                        <div class="item-subtotal">
                            $<?php echo number_format($subtotal, 2); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (5%)</span>
                        <span>$<?php echo number_format($total_price * 0.05, 2); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>$<?php echo number_format($total_price * 1.05, 2); ?></span>
                    </div>
                    <button class="btn-checkout">Proceed to Checkout</button>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>