<?php
session_start();

// Include database connection
include '../db/db_conn.php';

// Get User Role
$user_role = $_SESSION['user_role'] ?? 'client';

// AUTOMATIC SETUP: Create table and insert sample data if needed
$conn->query("CREATE TABLE IF NOT EXISTS pet_healthcare (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    stock_status VARCHAR(20) DEFAULT 'In Stock'
)");

$check = $conn->query("SELECT count(*) as count FROM pet_healthcare");
if ($check && $check->fetch_assoc()['count'] == 0) {
    $insertSql = "INSERT INTO pet_healthcare (name, category, price, image, description, stock_status) VALUES 
    ('Flea & Tick Spot-On', 'cat', 22.50, 'https://images.unsplash.com/photo-1596854407944-bf87f6fdd49e?auto=format&fit=crop&w=400&q=80', 'Monthly treatment to protect your cat.', 'In Stock'),
    ('Dental Health Treats', 'cat', 8.99, 'https://images.unsplash.com/photo-1571570891039-921399a831cb?auto=format&fit=crop&w=400&q=80', 'Helps reduce tartar and plaque buildup.', 'In Stock'),
    ('Hip & Joint Supplements', 'dog', 29.99, 'https://images.unsplash.com/photo-1587764379873-9781a94c836b?auto=format&fit=crop&w=400&q=80', 'Supports mobility for active or aging dogs.', 'In Stock'),
    ('Pet First-Aid Kit', 'dog', 19.99, 'https://images.unsplash.com/photo-1604917621955-1b35571ba882?auto=format&fit=crop&w=400&q=80', 'Essential supplies for minor injuries.', 'Low Stock'),
    ('Small Animal Nail Clippers', 'rabbit', 11.00, 'https://images.unsplash.com/photo-1591382386627-349b692688ff?auto=format&fit=crop&w=400&q=80', 'Safe and easy-to-use clippers for rabbits.', 'In Stock'),
    ('Soft Grooming Brush', 'rabbit', 9.50, 'https://images.unsplash.com/photo-1585110396063-8355845b3728?auto=format&fit=crop&w=400&q=80', 'Gentle brush to maintain a healthy coat.', 'In Stock'),
    ('Calcium + D3 Supplement', 'tortoise', 14.00, 'https://images.unsplash.com/photo-1535083252457-6080fe29be45?auto=format&fit=crop&w=400&q=80', 'Essential powder for shell health.', 'In Stock'),
    ('Reptile Wound Care', 'tortoise', 12.50, 'https://images.unsplash.com/photo-1437622368342-7a3d73a34c8f?auto=format&fit=crop&w=400&q=80', 'Antiseptic spray for minor cuts and scrapes.', 'Out of Stock')";
    $conn->query($insertSql);
}

// Handle Filtering
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';

if ($category_filter == 'all') {
    $sql = "SELECT * FROM pet_healthcare ORDER BY category, name";
} else {
    $category_filter = $conn->real_escape_string($category_filter);
    $sql = "SELECT * FROM pet_healthcare WHERE category = '$category_filter' ORDER BY name";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Healthcare - Pet Adoption Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pet_healthcare.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Pet Healthcare Shop 💊</h1>
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

        <div class="healthcare-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="healthcare-card">
                        <div class="image-wrapper">
                            <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://place-puppy.com/400x300'; ?>"
                                 alt="<?php echo htmlspecialchars($row['name']); ?>"
                                 class="healthcare-image">
                            <span class="category-badge"><?php echo ucfirst($row['category']); ?></span>
                        </div>
                        
                        <div class="healthcare-details">
                            <div class="healthcare-header">
                                <h3 class="healthcare-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <span class="healthcare-price">$<?php echo number_format($row['price'], 2); ?></span>
                            </div>

                            <p class="healthcare-description"><?php echo htmlspecialchars($row['description']); ?></p>

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
                    <h3>No healthcare items found in this category.</h3>
                    <p>Try selecting a different category or check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>