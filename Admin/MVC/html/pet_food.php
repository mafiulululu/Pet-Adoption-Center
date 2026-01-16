<?php
session_start();

// Include database connection
include '../db/db_conn.php';

// Get User Role
$user_role = $_SESSION['user_role'] ?? 'client';

// AUTOMATIC SETUP: Create table and insert sample data if needed
$conn->query("CREATE TABLE IF NOT EXISTS pet_food (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    stock_status VARCHAR(20) DEFAULT 'In Stock'
)");

$check = $conn->query("SELECT count(*) as count FROM pet_food");
if ($check && $check->fetch_assoc()['count'] == 0) {
    $insertSql = "INSERT INTO pet_food (name, category, price, image, description, stock_status) VALUES 
    ('Premium Salmon Kibble', 'cat', 18.99, 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?auto=format&fit=crop&w=400&q=80', 'Rich in Omega-3 for a shiny coat.', 'In Stock'),
    ('Gourmet Tuna Paté', 'cat', 2.50, 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=400&q=80', 'Soft texture, loved by picky eaters.', 'In Stock'),
    ('Steak & Potato Stew', 'dog', 24.99, 'https://images.unsplash.com/photo-1589924691195-41432c84c161?auto=format&fit=crop&w=400&q=80', 'Hearty chunks of beef with vegetables.', 'In Stock'),
    ('Puppy Growth Formula', 'dog', 19.50, 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=400&q=80', 'Essential nutrients for growing pups.', 'In Stock'),
    ('Timothy Hay Pellets', 'rabbit', 12.00, 'https://images.unsplash.com/photo-1585110396063-8355845b3728?auto=format&fit=crop&w=400&q=80', 'High fiber diet for healthy digestion.', 'In Stock'),
    ('Dried Fruit Treats', 'rabbit', 6.99, 'https://images.unsplash.com/photo-1518796745738-41048802f99a?auto=format&fit=crop&w=400&q=80', 'Natural sweet treats for bunnies.', 'Low Stock'),
    ('Calcium Sticks', 'tortoise', 14.50, 'https://images.unsplash.com/photo-1437622368342-7a3d73a34c8f?auto=format&fit=crop&w=400&q=80', 'Supports shell health and growth.', 'In Stock'),
    ('Organic Leaf Mix', 'tortoise', 9.00, 'https://images.unsplash.com/photo-1508455858334-95337ba25607?auto=format&fit=crop&w=400&q=80', 'Dried dandelion and plantain leaves.', 'In Stock')";
    $conn->query($insertSql);
}

// Handle Filtering
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';

if ($category_filter == 'all') {
    $sql = "SELECT * FROM pet_food ORDER BY category, name";
} else {
    $category_filter = $conn->real_escape_string($category_filter);
    $sql = "SELECT * FROM pet_food WHERE category = '$category_filter' ORDER BY name";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Food Shop - Pet Adoption Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pet_food.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Pet Food Shop 🍖</h1>
            <div style="display: flex; gap: 10px;">
                <a href="pet_toys.php" class="btn-back">Toys</a>
                <a href="pet_homes.php" class="btn-back">Homes</a>
                <a href="pet_healthcare.php" class="btn-back">Healthcare</a>
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

        <div class="food-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="food-card">
                        <div class="image-wrapper">
                            <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://place-puppy.com/400x300'; ?>"
                                 alt="<?php echo htmlspecialchars($row['name']); ?>"
                                 class="food-image">
                            <span class="category-badge"><?php echo ucfirst($row['category']); ?></span>
                        </div>
                        
                        <div class="food-details">
                            <div class="food-header">
                                <h3 class="food-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <span class="food-price">$<?php echo number_format($row['price'], 2); ?></span>
                            </div>

                            <p class="food-description"><?php echo htmlspecialchars($row['description']); ?></p>

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
                                    <input type="hidden" name="type" value="pet_food">
                                    <button type="submit" class="btn-buy" <?php echo strtolower($row['stock_status']) == 'out of stock' ? 'disabled' : ''; ?>>Add to Cart 🛒</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-items-message">
                    <h3>No food items found in this category.</h3>
                    <p>Try selecting a different category or check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>