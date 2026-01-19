<?php
session_start();

// Include database connection
include '../db/db_conn.php';

// User role
$user_role = $_SESSION['user_role'] ?? 'guest';

/* ===============================
   TABLE SETUP
   =============================== */
$conn->query("CREATE TABLE IF NOT EXISTS pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    breed VARCHAR(100) NOT NULL,
    age VARCHAR(50),
    adoption_status VARCHAR(20) DEFAULT 'available',
    species VARCHAR(20) NOT NULL,
    image VARCHAR(255)
)");

/* ===============================
   INSERT SAMPLE RABBIT DATA
   =============================== */
$check = $conn->query("SELECT COUNT(*) AS count FROM pets WHERE species = 'rabbit'");
if ($check && $check->fetch_assoc()['count'] == 0) {

    $sql = "INSERT INTO pets (name, breed, age, adoption_status, species, image) VALUES
        ('Thumper', 'Holland Lop', '1 year', 'available', 'rabbit', 'https://images.unsplash.com/photo-1585110396063-8355845b3728'),
        ('Oreo', 'Dutch Rabbit', '2 years', 'available', 'rabbit', 'https://images.unsplash.com/photo-1518796745738-41048802f99a'),
        ('Snowball', 'Lionhead', '6 months', 'adopted', 'rabbit', 'https://everbreed.com/wp-content/uploads/2024/05/2019-09-14_Lost_his_ball-2048x1365.jpg'),
        ('Hazel', 'Flemish Giant', '3 years', 'available', 'rabbit', 'https://images.unsplash.com/photo-1559214369-a6b1d7919865'),
        ('Cottontail', 'Netherland Dwarf', '1 year', 'pending', 'rabbit', 'https://images.unsplash.com/photo-1589952283406-b53a7d1347e8'),
        ('Bugs', 'Rex Rabbit', '2 years', 'available', 'rabbit', 'https://images.unsplash.com/photo-1591382386627-349b692688ff')";

    $conn->query($sql);
}

/* ===============================
   FETCH RABBITS
   =============================== */
$result = $conn->query("SELECT * FROM pets WHERE species = 'rabbit' ORDER BY adoption_status ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adopt a Rabbit 🐇</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/rabbits.css">
</head>
<body>

<div class="container">

    <div class="page-header">
        <h1 class="page-title">Adopt a Rabbit 🐇</h1>
        <a href="home.php" class="btn-adopt">Back to Home</a>
    </div>

    <div class="rabbit-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="rabbit-card">
                    <img src="<?= !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://place-puppy.com/400x300'; ?>"
                         alt="<?= htmlspecialchars($row['name']); ?>"
                         class="rabbit-image">

                    <div class="rabbit-details">
                        <h3><?= htmlspecialchars($row['name']); ?></h3>
                        <p><strong>Breed:</strong> <?= htmlspecialchars($row['breed']); ?></p>
                        <p><strong>Age:</strong> <?= htmlspecialchars($row['age']); ?></p>

                        <?php
                        $status = strtolower($row['adoption_status']);
                        $statusClass = match ($status) {
                            'adopted' => 'status-adopted',
                            'pending' => 'status-pending',
                            default => 'status-available'
                        };
                        ?>
                        <span class="status-badge <?= $statusClass; ?>">
                            <?= ucfirst($status); ?>
                        </span>

                        <div class="actions">
                            <?php if ($user_role === 'admin'): ?>
                                <a href="edit_pet.php?id=<?= $row['id']; ?>" class="btn-adopt edit">Edit</a>
                                <a href="delete_pet.php?id=<?= $row['id']; ?>"
                                   class="btn-adopt delete"
                                   onclick="return confirm('Delete this pet?');">Delete</a>

                            <?php elseif ($user_role === 'worker'): ?>
                                <a href="care_status.php?id=<?= $row['id']; ?>" class="btn-adopt">Update Care</a>

                            <?php elseif ($status === 'available'): ?>
                                <a href="adoption_form.php?id=<?= $row['pet_id']; ?>" class="btn-adopt">Adopt Me</a>

                            <?php else: ?>
                                <button class="btn-adopt disabled" disabled>Not Available</button>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No rabbits available for adoption right now.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
