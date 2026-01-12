<?php
session_start();

// Include database connection
include '../db/db_conn.php';

// Get User Role
$user_role = $_SESSION['user_role'] ?? 'client';

// AUTOMATIC SETUP: Create table and insert sample data if needed
// Note: Table structure is defined in schema.sql
$check = $conn->query("SELECT count(*) as count FROM pets WHERE species = 'cat'");
if ($check && $check->fetch_assoc()['count'] == 0) {
    // Insert sample data matching the new schema (age is INT, species instead of type)
    $insertSql = "INSERT INTO pets (name, breed, age, adoption_status, species, image, gender, color, health_status) VALUES 
        ('Luna', 'Siamese', 2, 'available', 'cat', 'https://images.unsplash.com/photo-1513245543132-31f507417b26?auto=format&fit=crop&w=400&q=80', 'female', 'Cream', 'Healthy'),
        ('Oliver', 'Maine Coon', 3, 'available', 'cat', 'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?auto=format&fit=crop&w=400&q=80', 'male', 'Tabby', 'Healthy'),
        ('Bella', 'Persian', 4, 'adopted', 'cat', 'https://images.unsplash.com/photo-1573865526739-10659fec78a5?auto=format&fit=crop&w=400&q=80', 'female', 'White', 'Healthy'),
        ('Leo', 'Bengal', 1, 'available', 'cat', 'https://images.unsplash.com/photo-1519052537078-e6302a4968d4?auto=format&fit=crop&w=400&q=80', 'male', 'Spotted', 'Vaccinated'),
        ('Milo', 'Scottish Fold', 2, 'pending', 'cat', 'https://images.unsplash.com/photo-1574158622682-e40e69881006?auto=format&fit=crop&w=400&q=80', 'male', 'Grey', 'Healthy'),
        ('Cleo', 'Sphynx', 3, 'available', 'cat', 'https://images.unsplash.com/photo-1518791841217-8f162f1e1131?auto=format&fit=crop&w=400&q=80', 'female', 'Pink', 'Special Diet')";
    
    $conn->query($insertSql);
}

// Fetch cats from the database
$sql = "SELECT * FROM pets WHERE species = 'cat' ORDER BY adoption_status ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Cats - Pet Adoption Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/cats.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Adopt a Cat 🐱</h1>
            <a href="home.php" class="btn-adopt" style="width: auto; margin: 0; padding: 0.6rem 1.2rem;">Back to Home-Page</a>
        </div>

        <div class="cat-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="cat-card">
                        <!-- Display Image (Use placeholder if empty) -->
                        <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://placekitten.com/400/300'; ?>" 
                             alt="<?php echo htmlspecialchars($row['name']); ?>" 
                             class="cat-image">
                        
                        <div class="cat-details">
                            <h3 class="cat-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                            
                            <div class="cat-meta">
                                <span>Breed:</span>
                                <strong><?php echo htmlspecialchars($row['breed']); ?></strong>
                            </div>
                            
                            <div class="cat-meta">
                                <span>Age:</span>
                                <strong><?php echo htmlspecialchars($row['age']); ?> years</strong>
                            </div>

                            <?php 
                                $statusClass = 'status-available';
                                $status = strtolower($row['adoption_status']);
                                if($status == 'adopted') $statusClass = 'status-adopted';
                                if($status == 'pending') $statusClass = 'status-pending';
                            ?>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo ucfirst(htmlspecialchars($row['adoption_status'])); ?>
                            </span>

                            <!-- Role Based Actions -->
                            <?php if ($user_role === 'admin'): ?>
                                <div class="admin-actions" style="margin-top: 10px;">
                                    <a href="edit_pet.php?id=<?php echo $row['pet_id']; ?>" class="btn-adopt" style="background-color: #f39c12;">Edit</a>
                                    <a href="delete_pet.php?id=<?php echo $row['pet_id']; ?>" class="btn-adopt" style="background-color: #e74c3c;" onclick="return confirm('Delete this pet?')">Delete</a>
                                </div>
                            <?php elseif ($user_role === 'worker'): ?>
                                <div class="worker-actions" style="margin-top: 10px;">
                                    <a href="care_status.php?id=<?php echo $row['pet_id']; ?>" class="btn-adopt" style="background-color: #3498db;">Update Care</a>
                                </div>
                            <?php elseif($status === 'available'): ?>
                                <a href="adopt_process.php?id=<?php echo $row['pet_id']; ?>" class="btn-adopt">Adopt Me</a>
                            <?php else: ?>
                                <button class="btn-adopt disabled">Not Available</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-cats-message">
                    <h3>No cats available for adoption right now.</h3>
                    <p>Please check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>