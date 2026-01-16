<?php
session_start();

// Include database connection
include '../db/db_conn.php';

// Get User Role
$user_role = $_SESSION['user_role'] ?? 'client';

// AUTOMATIC SETUP: Create table and insert sample data if needed
$conn->query("CREATE TABLE IF NOT EXISTS pet_homes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    stock_status VARCHAR(20) DEFAULT 'In Stock'
)");

$check = $conn->query("SELECT count(*) as count FROM pet_homes");
if ($check && $check->fetch_assoc()['count'] == 0) {
    $insertSql = "INSERT INTO pet_homes (name, category, price, image, description, stock_status) VALUES 
    ('Luxury Cat Tree Tower', 'cat', 89.99, 'https://images.unsplash.com/photo-1545249390-6bdfa286032f?auto=format&fit=crop&w=400&q=80', 'Multi-level condo with scratching posts.', 'In Stock'),
    ('Cozy Igloo Bed', 'cat', 25.50, 'https://images.unsplash.com/photo-1513245543132-31f507417b26?auto=format&fit=crop&w=400&q=80', 'Soft and warm hideaway for cats.', 'In Stock'),
    ('Large Wooden Dog Kennel', 'dog', 149.99, 'https://images.unsplash.com/photo-1591946614720-90a587da4a36?auto=format&fit=crop&w=400&q=80', 'Weather-resistant outdoor shelter.', 'In Stock'),
    ('Orthopedic Memory Foam Bed', 'dog', 45.00, 'https://images.unsplash.com/photo-1581888227599-77981198520d?auto=format&fit=crop&w=400&q=80', 'Supports joints for older dogs.', 'Low Stock'),
    ('Two-Story Rabbit Hutch', 'rabbit', 110.00, 'https://images.unsplash.com/photo-1585110396063-8355845b3728?auto=format&fit=crop&w=400&q=80', 'Spacious wooden hutch with run.', 'In Stock'),
    ('Indoor Cage Setup', 'rabbit', 75.99, 'https://images.unsplash.com/photo-1559214369-a6b1d7919865?auto=format&fit=crop&w=400&q=80', 'Secure indoor living space.', 'In Stock'),
    ('Tortoise Table Enclosure', 'tortoise', 95.50, 'https://images.unsplash.com/photo-1437622368342-7a3d73a34c8f?auto=format&fit=crop&w=400&q=80', 'Open-top wooden table for ventilation.', 'In Stock'),
    ('Outdoor Pen with Mesh', 'tortoise', 55.00, 'https://images.unsplash.com/photo-1508455858334-95337ba25607?auto=format&fit=crop&w=400&q=80', 'Safe outdoor grazing area.', 'Out of Stock')";
    $conn->query($insertSql);
}

// Handle Filtering
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';

if ($category_filter == 'all') {
    $sql = "SELECT * FROM pet_homes ORDER BY category, name";
} else {
    $category_filter = $conn->real_escape_string($category_filter);
    $sql = "SELECT * FROM pet_homes WHERE category = '$category_filter' ORDER BY name";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Homes Shop - Pet Adoption Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pet_homes.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Pet Homes Shop 🏠</h1>
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

        <div class="home-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="home-card">
                        <div class="image-wrapper">
                            <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://place-puppy.com/400x300'; ?>"
                                 alt="<?php echo htmlspecialchars($row['name']); ?>"
                                 class="home-image">
                            <span class="category-badge"><?php echo ucfirst($row['category']); ?></span>
                        </div>
                        
                        <div class="home-details">
                            <div class="home-header">
                                <h3 class="home-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <span class="home-price">$<?php echo number_format($row['price'], 2); ?></span>
                            </div>

                            <p class="home-description"><?php echo htmlspecialchars($row['description']); ?></p>

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
                                    <input type="hidden" name="type" value="pet_homes">
                                    <button type="submit" class="btn-buy" <?php echo strtolower($row['stock_status']) == 'out of stock' ? 'disabled' : ''; ?>>Add to Cart 🛒</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-items-message">
                    <h3>No homes found in this category.</h3>
                    <p>Try selecting a different category or check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>