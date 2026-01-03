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
 
$check = $conn->query("SELECT count(*) as count FROM pets WHERE type = 'rabbit'");
if ($check && $check->fetch_assoc()['count'] == 0) {
    $insertSql = "INSERT INTO pets (name, breed, age, status, type, image) VALUES
        ('Thumper', 'Holland Lop', '1 year', 'available', 'rabbit', 'https://images.unsplash.com/photo-1585110396063-8355845b3728?auto=format&fit=crop&w=400&q=80%27),
        ('Oreo', 'Dutch Rabbit', '2 years', 'available', 'rabbit', 'https://images.unsplash.com/photo-1518796745738-41048802f99a?auto=format&fit=crop&w=400&q=80%27),
        ('Snowball', 'Lionhead', '6 months', 'adopted', 'rabbit', 'https://images.unsplash.com/photo-1535241556843-adbd92c4e673?auto=format&fit=crop&w=400&q=80%27),
        ('Hazel', 'Flemish Giant', '3 years', 'available', 'rabbit', 'https://images.unsplash.com/photo-1559214369-a6b1d7919865?auto=format&fit=crop&w=400&q=80%27),
        ('Cottontail', 'Netherland Dwarf', '1 year', 'pending', 'rabbit', 'https://images.unsplash.com/photo-1589952283406-b53a7d1347e8?auto=format&fit=crop&w=400&q=80%27),
        ('Bugs', 'Rex Rabbit', '2 years', 'available', 'rabbit', 'https://images.unsplash.com/photo-1591382386627-349b692688ff?auto=format&fit=crop&w=400&q=80%27)";
 
    $conn->query($insertSql);
}
 
// Fetch rabbits from the database
$sql = "SELECT * FROM pets WHERE type = 'rabbit' ORDER BY status ASC";
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
                <div class="no-rabbits-message">
                    <h3>No rabbits available for adoption right now.</h3>
                    <p>Please check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
 
</body>
</html>