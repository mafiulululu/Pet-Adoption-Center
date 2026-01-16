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

$check = $conn->query("SELECT count(*) as count FROM pet_toys");
if ($check && $check->fetch_assoc()['count'] == 0) {
    $insertSql = "INSERT INTO pet_toys (name, category, price, image, description, stock_status) VALUES 
    ('Feather Wand Teaser', 'cat', 7.99, 'https://images.unsplash.com/photo-1592194993193-e6a4c118f8f2?auto=format&fit=crop&w=400&q=80', 'Interactive fun for you and your cat.', 'In Stock'),
    ('Catnip Mouse Trio', 'cat', 5.50, 'https://images.unsplash.com/photo-1548681528-6a5c45b66b42?auto=format&fit=crop&w=400&q=80', 'Filled with premium organic catnip.', 'In Stock'),
    ('Durable Squeaky Ball', 'dog', 9.99, 'https://images.unsplash.com/photo-1529429617124-95b109e86bb8?auto=format&fit=crop&w=400&q=80', 'A tough ball for endless games of fetch.', 'In Stock'),
    ('Rope Tug Toy', 'dog', 12.50, 'https://images.unsplash.com/photo-1612536037443-d42c42935438?auto=format&fit=crop&w=400&q=80', 'Great for dental health and tug-of-war.', 'Low Stock'),
    ('Willow Chew Ball', 'rabbit', 6.00, 'https://images.unsplash.com/photo-1591382386627-349b692688ff?auto=format&fit=crop&w=400&q=80', '100% natural willow, safe for chewing.', 'In Stock'),
    ('Foraging Mat', 'rabbit', 15.99, 'https://images.unsplash.com/photo-1589952283406-b53a7d1347e8?auto=format&fit=crop&w=400&q=80', 'Encourages natural foraging instincts.', 'In Stock'),
    ('Treat Dispensing Ball', 'tortoise', 11.50, 'https://images.unsplash.com/photo-1535083252457-6080fe29be45?auto=format&fit=crop&w=400&q=80', 'A slow-release treat ball for enrichment.', 'In Stock'),
    ('Smooth Basking Rock', 'tortoise', 18.00, 'https://images.unsplash.com/photo-1482401634921-fdeb808e6f7f?auto=format&fit=crop&w=400&q=80', 'A comfortable and safe basking spot.', 'Out of Stock')";
    $conn->query($insertSql);
}

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
            <a href="pet_food.php" class="btn-back" style="margin-right: 10px;">Shop Pet Food</a>
            <a href="home.php" class="btn-back">Back to Home</a>
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
                                <button class="btn-buy" <?php echo strtolower($row['stock_status']) == 'out of stock' ? 'disabled' : ''; ?>>
                                    Add to Cart 🛒
                                </button>
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