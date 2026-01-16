<?php
session_start();

// Include database connection
include '../db/db_conn.php';

// Get User Role
$user_role = $_SESSION['user_role'] ?? 'client';

// AUTOMATIC SETUP: Create table and insert sample data if needed
$conn->query("CREATE TABLE IF NOT EXISTS pet_toys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    stock_status VARCHAR(20) DEFAULT 'In Stock'
)");

// Handle Filtering
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';

if ($category_filter == 'all') {
    $sql = "SELECT * FROM pet_toys ORDER BY category, name";
} else {
    $category_filter = $conn->real_escape_string($category_filter);
    $sql = "SELECT * FROM pet_toys WHERE category = '$category_filter' ORDER BY name";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Toys Shop - Pet Adoption Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pet_toys.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Pet Toys Shop 🎾</h1>
            <div style="display: flex; gap: 10px;">
                <a href="pet_food.php" class="btn-back">Shop Pet Food</a>
                <a href="home.php" class="btn-back">Back to Home</a>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="filter-container">
            <a href="?category=all" class="filter-btn <?php echo $category_filter == 'all' ? 'active' : ''; ?>">All</a>
            <a href="?category=cat" class="filter-btn <?php echo $category_filter == 'cat' ? 'active' : ''; ?>">🐱 Cats</a>
            <a href="?category=dog" class="filter-btn <?php echo $category_filter == 'dog' ? 'active' : ''; ?>">🐕 Dogs</a>
            <a href="?category=rabbit" class="filter-btn <?php echo $category_filter == 'rabbit' ? 'active' : ''; ?>">🐇 Rabbits</a>
            <a href="?category=tortoise" class="filter-btn <?php echo $category_filter == 'tortoise' ? 'active' : ''; ?>">🐢 Tortoises</a>
        </div>

        <div class="toy-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="toy-card">
                        <div class="image-wrapper">
                            <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://place-puppy.com/400x300'; ?>"
                                 alt="<?php echo htmlspecialchars($row['name']); ?>"
                                 class="toy-image">
                            <span class="category-badge"><?php echo ucfirst($row['category']); ?></span>
                        </div>
                        
                        <div class="toy-details">
                            <div class="toy-header">
                                <h3 class="toy-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <span class="toy-price">$<?php echo number_format($row['price'], 2); ?></span>
                            </div>

                            <p class="toy-description"><?php echo htmlspecialchars($row['description']); ?></p>

                            <?php
                                $stockClass = 'stock-in';
                                if(strtolower($row['stock_status']) == 'out of stock') $stockClass = 'stock-out';
                                if(strtolower($row['stock_status']) == 'low stock') $stockClass = 'stock-low';
                            ?>
                            <div class="stock-status <?php echo $stockClass; ?>">
                                ● <?php echo htmlspecialchars($row['stock_status']); ?>
                            </div>

                            <!-- Role Based Actions -->
                            <?php if ($user_role === 'admin'): ?>
                                <div class="admin-actions">
                                    <button class="btn-action btn-edit">Edit</button>
                                    <button class="btn-action btn-delete" onclick="return confirm('Delete this item?')">Delete</button>
                                </div>
                            <?php else: ?>
                                <form action="cart_action.php" method="POST">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="type" value="pet_toys">
                                    <button type="submit" class="btn-buy" <?php echo strtolower($row['stock_status']) == 'out of stock' ? 'disabled' : ''; ?>>Add to Cart 🛒</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-items-message">
                    <h3>No toys found in this category.</h3>
                    <p>Try selecting a different category or check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>