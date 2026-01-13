<?php
session_start();

// Include database connection
include '../db/db_conn.php';

// Get User Role
$user_role = $_SESSION['user_role'] ?? 'client';

// AUTOMATIC SETUP: Create table and insert sample data if needed
$conn->query("CREATE TABLE IF NOT EXISTS pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    breed VARCHAR(100) NOT NULL,
    age VARCHAR(50),
    status VARCHAR(20) DEFAULT 'available',
    type VARCHAR(20) NOT NULL,
    image VARCHAR(255)
)");

$check = $conn->query("SELECT count(*) as count FROM pets WHERE type = 'tortoise'");
if ($check && $check->fetch_assoc()['count'] == 0) {
    $insertSql = "INSERT INTO pets (name, breed, age, status, type, image) VALUES
        ('Sheldon', 'Sulcata', '5 years', 'available', 'tortoise', 'https://images.unsplash.com/photo-1508455858334-95337ba25607?auto=format&fit=crop&w=400&q=80'),
        ('Tank', 'Leopard Tortoise', '10 years', 'available', 'tortoise', 'https://images.unsplash.com/photo-1482401634921-fdeb808e6f7f?auto=format&fit=crop&w=400&q=80'),
        ('Speedy', 'Hermann\'s Tortoise', '3 years', 'adopted', 'tortoise', 'https://images.unsplash.com/photo-1535083252457-6080fe29be45?auto=format&fit=crop&w=400&q=80'),
        ('Oogway', 'Aldabra Giant', '50 years', 'available', 'tortoise', 'https://images.unsplash.com/photo-1437622368342-7a3d73a34c8f?auto=format&fit=crop&w=400&q=80'),
        ('Franklin', 'Box Turtle', '4 years', 'pending', 'tortoise', 'https://images.unsplash.com/photo-1518791841217-8f162f1e1131?auto=format&fit=crop&w=400&q=80'),
        ('Shelly', 'Russian Tortoise', '6 years', 'available', 'tortoise', 'https://images.unsplash.com/photo-1559214369-a6b1d7919865?auto=format&fit=crop&w=400&q=80')";

    $conn->query($insertSql);
}

// Fetch tortoises from the database
$sql = "SELECT * FROM pets WHERE type = 'tortoise' ORDER BY status ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Tortoises - Pet Adoption Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/tortoises.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Adopt a Tortoise 🐢</h1>
            <a href="home.php" class="btn-adopt" style="width: auto; margin: 0; padding: 0.6rem 1.2rem;">Back to Home-Page</a>
        </div>

        <div class="tortoise-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="tortoise-card">
                        <!-- Display Image (Use placeholder if empty) -->
                        <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://place-puppy.com/400x300'; ?>"
                             alt="<?php echo htmlspecialchars($row['name']); ?>"
                             class="tortoise-image">
                        
                        <div class="tortoise-details">
                            <h3 class="tortoise-name"><?php echo htmlspecialchars($row['name']); ?></h3>

                            <div class="tortoise-meta">
                                <span>Breed:</span>
                                <strong><?php echo htmlspecialchars($row['breed']); ?></strong>
                            </div>

                            <div class="tortoise-meta">
                                <span>Age:</span>
                                <strong><?php echo htmlspecialchars($row['age']); ?></strong>
                            </div>

                            <?php
                                $statusClass = 'status-available';
                                $status = strtolower($row['status']);
                                if($status == 'adopted') $statusClass = 'status-adopted';
                                if($status == 'pending') $statusClass = 'status-pending';
                            ?>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                            </span>

                            <!-- Role Based Actions -->
                            <?php if ($user_role === 'admin'): ?>
                                <div class="admin-actions" style="margin-top: 10px;">
                                    <a href="edit_pet.php?id=<?php echo $row['id']; ?>" class="btn-adopt" style="background-color: #f39c12;">Edit</a>
                                    <a href="delete_pet.php?id=<?php echo $row['id']; ?>" class="btn-adopt" style="background-color: #e74c3c;" onclick="return confirm('Delete this pet?')">Delete</a>
                                </div>
                            <?php elseif ($user_role === 'worker'): ?>
                                <div class="worker-actions" style="margin-top: 10px;">
                                    <a href="care_status.php?id=<?php echo $row['id']; ?>" class="btn-adopt" style="background-color: #3498db;">Update Care</a>
                                </div>
                            <?php elseif($status === 'available'): ?>
                                <a href="adopt_process.php?id=<?php echo $row['id']; ?>" class="btn-adopt">Adopt Me</a>
                            <?php else: ?>
                                <button class="btn-adopt disabled">Not Available</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-tortoises-message">
                    <h3>No tortoises available for adoption right now.</h3>
                    <p>Please check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>