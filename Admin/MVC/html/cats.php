<?php
session_start();

// Include database connection
include '../db/db_conn.php';

// Optional: Redirect if not logged in
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

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

$check = $conn->query("SELECT count(*) as count FROM pets WHERE type = 'cat'");
if ($check && $check->fetch_assoc()['count'] == 0) {
    $insertSql = "INSERT INTO pets (name, breed, age, status, type, image) VALUES 
        ('Luna', 'Siamese', '2 years', 'available', 'cat', 'https://images.unsplash.com/photo-1513245543132-31f507417b26?auto=format&fit=crop&w=400&q=80'),
        ('Oliver', 'Maine Coon', '3 years', 'available', 'cat', 'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?auto=format&fit=crop&w=400&q=80'),
        ('Bella', 'Persian', '4 years', 'adopted', 'cat', 'https://images.unsplash.com/photo-1573865526739-10659fec78a5?auto=format&fit=crop&w=400&q=80'),
        ('Leo', 'Bengal', '1 year', 'available', 'cat', 'https://images.unsplash.com/photo-1519052537078-e6302a4968d4?auto=format&fit=crop&w=400&q=80'),
        ('Milo', 'Scottish Fold', '2 years', 'pending', 'cat', 'https://images.unsplash.com/photo-1574158622682-e40e69881006?auto=format&fit=crop&w=400&q=80'),
        ('Cleo', 'Sphynx', '3 years', 'available', 'cat', 'https://images.unsplash.com/photo-1518791841217-8f162f1e1131?auto=format&fit=crop&w=400&q=80')";
    
    $conn->query($insertSql);
}

// Fetch cats from the database
// NOTE: Ensure you have a 'pets' table with columns: id, name, breed, age, status, type, image
$sql = "SELECT * FROM pets WHERE type = 'cat' ORDER BY status ASC";
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
                    <h3>No cats available for adoption right now.</h3>
                    <p>Please check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>