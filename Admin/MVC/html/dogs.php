<?php
session_start();
 
// Include database connection
include '../db/db_conn.php';
 
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
 
$check = $conn->query("SELECT count(*) as count FROM pets WHERE type = 'dog'");
if ($check && $check->fetch_assoc()['count'] == 0) {
    $insertSql = "INSERT INTO pets (name, breed, age, status, type, image) VALUES
        ('Buddy', 'Golden Retriever', '3 years', 'available', 'dog', 'https://images.unsplash.com/photo-1546685299-e0090bacd7e2?auto=format&fit=crop&w=400&q=80%27),
        ('Lucy', 'Labrador', '4 years', 'available', 'dog', 'https://images.unsplash.com/photo-1583511655826-05700d52f4d9?auto=format&fit=crop&w=400&q=80%27),
        ('Charlie', 'German Shepherd', '2 years', 'adopted', 'dog', 'https://images.unsplash.com/photo-1560807707-8cc7568bd579?auto=format&fit=crop&w=400&q=80%27),
        ('Daisy', 'Beagle', '1 year', 'available', 'dog', 'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=400&q=80%27),
        ('Rocky', 'Bulldog', '5 years', 'pending', 'dog', 'https://images.unsplash.com/photo-1568554133264-7996ba14ca11?auto=format&fit=crop&w=400&q=80%27),
        ('Coco', 'Poodle', '2 years', 'available', 'dog', 'https://images.unsplash.com/photo-1543466835-00a7907ca9be?auto=format&fit=crop&w=400&q=80%27)";
 
    $conn->query($insertSql);
}
 
// Fetch dogs from the database
$sql = "SELECT * FROM pets WHERE type = 'dog' ORDER BY status ASC";
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
 
        <div class="cat-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="dog-card">
                        <!-- Display Image (Use placeholder if empty) -->
                        <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://place-puppy.com/400x300'; ?>"
                             alt="<?php echo htmlspecialchars($row['name']); ?>"
                             class="dog-image">
                         
                        <div class="cat-details">
                            <h3 class="cat-name"><?php echo htmlspecialchars($row['name']); ?></h3>
 
                            <div class="dog-meta">
                                <span>Breed:</span>
                                <strong><?php echo htmlspecialchars($row['breed']); ?></strong>
                            </div>
 
                            <div class="dog-meta">
                                <span>Age:</span>
                                <strong><?php echo htmlspecialchars($row['age']); ?></strong>
                            </div>
 
                            <?php
                                $statusClass = 'status-available';
                                if(strtolower($row['status']) == 'adopted') $statusClass = 'status-adopted';
                                if(strtolower($row['status']) == 'pending') $statusClass = 'status-pending';
                            ?>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                            </span>
 
                            <?php if(strtolower($row['status']) === 'available'): ?>
                                <a href="adopt_process.php?id=<?php echo $row['id']; ?>" class="btn-adopt">Adopt Me</a>
                            <?php else: ?>
                                <button class="btn-adopt disabled">Not Available</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-cats-message">
                    <h3>No dogs available for adoption right now.</h3>
                    <p>Please check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
 
</body>
</html>