<?php
session_start();
 
// Include database connection
include '../db/db_conn.php';
$user_role = $_SESSION['user_role'] ?? 'guest';

 
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
 
$check = $conn->query("SELECT count(*) as count FROM pets WHERE species = 'dog'");
if ($check && $check->fetch_assoc()['count'] == 0) {
    $insertSql = "INSERT INTO pets (name, breed, age, adoption_status, species, image) VALUES
        ('Buddy', 'Golden Retriever', '3 years', 'available', 'dog', 'https://images.unsplash.com/photo-1601758125946-6ec2ef64daf8?auto=format&fit=crop&w=800&q=80),
        ('Lucy', 'Labrador', '4 years', 'available', 'dog', 'https://images.unsplash.com/photo-1583511655826-05700d52f4d9?auto=format&fit=crop&w=400&q=80%27),
        ('Charlie', 'German Shepherd', '2 years', 'adopted', 'dog', 'https://images.unsplash.com/photo-1560807707-8cc7568bd579?auto=format&fit=crop&w=400&q=80%27),
        ('Daisy', 'Beagle', '1 year', 'available', 'dog', 'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=400&q=80%27),
        ('Rocky', 'Bulldog', '5 years', 'pending', 'dog', 'https://images.unsplash.com/photo-1568554133264-7996ba14ca11?auto=format&fit=crop&w=400&q=80%27),
        ('Coco', 'Poodle', '2 years', 'available', 'dog', 'https://images.unsplash.com/photo-1543466835-00a7907ca9be?auto=format&fit=crop&w=400&q=80%27)";
 
    $conn->query($insertSql);
}
 
// Fetch dogs from the database
$sql = "SELECT * FROM pets WHERE species = 'dog'";
$result = $conn->query($sql);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Dogs - Pet Adoption Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dogs.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
 
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Adopt a Dog 🐶</h1>
            <a href="home.php" class="btn-adopt" style="width: auto; margin: 0; padding: 0.6rem 1.2rem;">Back to Home-Page</a>
        </div>
 
        <div class="dog-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="dog-card">
                        <!-- Display Image (Use placeholder if empty) -->
                        <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://place-puppy.com/400x300'; ?>"
                             alt="<?php echo htmlspecialchars($row['name']); ?>"
                             class="dog-image">
                         
                        <div class="dog-details">
                            <h3 class="dog-name"><?php echo htmlspecialchars($row['name']); ?></h3>
 
                            <div class="dog-meta">
                                <span>Breed:</span>
                                <strong><?php echo htmlspecialchars($row['breed']); ?></strong>
                            </div>
 
                            <div class="dog-meta">
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
                                    <a href="edit_pet.php?id=<?php echo $row['id']; ?>" class="btn-adopt" style="background-color: #f39c12;">Edit</a>
                                    <a href="delete_pet.php?id=<?php echo $row['id']; ?>" class="btn-adopt" style="background-color: #e74c3c;" onclick="return confirm('Delete this pet?')">Delete</a>
                                </div>
                            <?php elseif ($user_role === 'worker'): ?>
                                <div class="worker-actions" style="margin-top: 10px;">
                                    <a href="care_status.php?id=<?php echo $row['id']; ?>" class="btn-adopt" style="background-color: #3498db;">Update Care</a>
                                </div>
                            <?php elseif($status === 'available'): ?>
                                <a href="adoption_form.php?id=<?= $row['id']; ?>" class="btn-adopt">
                                    Adopt Me
                                </a>
                            <?php else: ?>
                                <button class="btn-adopt disabled">Not Available</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-dogs-message">
                    <h3>No dogs available for adoption right now.</h3>
                    <p>Please check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
 
</body>
</html>