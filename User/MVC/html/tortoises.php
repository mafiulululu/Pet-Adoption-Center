<?php
session_start();

// Database connection
include '../db/db_conn.php';

// User role
$user_role = $_SESSION['user_role'] ?? 'client';

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
   INSERT SAMPLE TORTOISE DATA
   =============================== */
$check = $conn->query("SELECT COUNT(*) AS count FROM pets WHERE species = 'tortoise'");
if ($check && $check->fetch_assoc()['count'] == 0) {

    $sql = "INSERT INTO pets (name, breed, age, adoption_status, species, image) VALUES
        ('Sheldon', 'Sulcata', '5 years', 'available', 'tortoise', 'https://images.unsplash.com/photo-1508455858334-95337ba25607'),
        ('Tank', 'Leopard Tortoise', '10 years', 'available', 'tortoise', 'https://b-cdn.springnest.com/media/img/9u/4ceddf87.png?width=1240&height=826&fit=crop'),
        ('Speedy', 'Hermann\'s Tortoise', '3 years', 'adopted', 'tortoise', 'https://images.unsplash.com/photo-1535083252457-6080fe29be45'),
        ('Oogway', 'Aldabra Giant', '50 years', 'available', 'tortoise', 'https://images.unsplash.com/photo-1437622368342-7a3d73a34c8f'),
        ('Shelly', 'Russian Tortoise', '6 years', 'available', 'tortoise', 'https://images.unsplash.com/photo-1559214369-a6b1d7919865')";

    $conn->query($sql);
}

/* ===============================
   FETCH TORTOISES
   =============================== */
$result = $conn->query("SELECT * FROM pets WHERE species = 'tortoise' ORDER BY adoption_status ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adopt a Tortoise 🐢</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/tortoises.css">
</head>
<body>

<div class="container">

    <div class="page-header">
        <h1 class="page-title">Adopt a Tortoise 🐢</h1>
        <a href="home.php" class="btn-adopt">Back to Home</a>
    </div>

    <div class="tortoise-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="tortoise-card">
                    <img src="<?= !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://place-puppy.com/400x300'; ?>"
                         alt="<?= htmlspecialchars($row['name']); ?>"
                         class="tortoise-image">

                    <div class="tortoise-details">
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
            <p>No tortoises available for adoption right now.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
