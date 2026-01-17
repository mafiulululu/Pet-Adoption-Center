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
    adoption_status VARCHAR(20) DEFAULT 'available',
    species VARCHAR(20) NOT NULL,
    image VARCHAR(255)
)");
 
$check = $conn->query("SELECT count(*) as count FROM pets WHERE species = 'rabbit'");
if ($check && $check->fetch_assoc()['count'] == 0) {
    $insertSql = "INSERT INTO pets (name, breed, age, adoption_status, species, image) VALUES
        ('Thumper', 'Holland Lop', '1 year', 'available', 'rabbit', 'https://images.unsplash.com/photo-1585110396063-8355845b3728?auto=format&fit=crop&w=400&q=80'),
        ('Oreo', 'Dutch Rabbit', '2 years', 'available', 'rabbit', 'https://images.unsplash.com/photo-1518796745738-41048802f99a?auto=format&fit=crop&w=400&q=80'),
        ('Snowball', 'Lionhead', '6 months', 'adopted', 'rabbit', 'https://images.unsplash.com/photo-1535241556843-adbd92c4e673?auto=format&fit=crop&w=400&q=80'),
        ('Hazel', 'Flemish Giant', '3 years', 'available', 'rabbit', 'https://images.unsplash.com/photo-1559214369-a6b1d7919865?auto=format&fit=crop&w=400&q=80'),
        ('Cottontail', 'Netherland Dwarf', '1 year', 'pending', 'rabbit', 'https://images.unsplash.com/photo-1589952283406-b53a7d1347e8?auto=format&fit=crop&w=400&q=80'),
        ('Bugs', 'Rex Rabbit', '2 years', 'available', 'rabbit', 'https://images.unsplash.com/photo-1591382386627-349b692688ff?auto=format&fit=crop&w=400&q=80')";
 
    $conn->query($insertSql);
}
 
// Fetch rabbits from the database
$sql = "SELECT * FROM pets WHERE species = 'rabbit' ORDER BY adoption_status ASC";
$result = $conn->query($sql);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rabbits - Pet Adoption Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/rabbits.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
 
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Adopt a Rabbit 🐇</h1>
            <a href="home.php" class="btn-adopt" style="width: auto; margin: 0; padding: 0.6rem 1.2rem;">Back to Home-Page</a>
        </div>
 
        <div class="rabbit-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="rabbit-card">
                        <!-- Display Image (Use placeholder if empty) -->
                        <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://place-puppy.com/400x300'; ?>"
                             alt="<?php echo htmlspecialchars($row['name']); ?>"
                             class="rabbit-image">
                         
                        <div class="rabbit-details">
                            <h3 class="rabbit-name"><?php echo htmlspecialchars($row['name']); ?></h3>
 
                            <div class="rabbit-meta">
                                <span>Breed:</span>
                                <strong><?php echo htmlspecialchars($row['breed']); ?></strong>
                            </div>
 
                            <div class="rabbit-meta">
                                <span>Age:</span>
                                <strong><?php echo htmlspecialchars($row['age']); ?></strong>
                            </div>
 
                            <?php
                                $statusClass = 'status-available';
                                // Handle potential schema differences
                                $status = $row['adoption_status'];
                                $status = strtolower($status);

                                if($status == 'adopted') $statusClass = 'status-adopted';
                                if($status == 'pending') $statusClass = 'status-pending';
                            ?>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo ucfirst(htmlspecialchars($status)); ?>
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
                <div class="no-rabbits-message">
                    <h3>No rabbits available for adoption right now.</h3>
                    <p>Please check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
 
</body>
</html>