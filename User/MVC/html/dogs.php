<?php
session_start();

// Database connection
include '../db/db_conn.php';

// User role
$user_role = $_SESSION['user_role'] ?? 'guest';

/* ===============================
   TABLE SETUP (same as cats.php)
   =============================== */
$conn->query("CREATE TABLE IF NOT EXISTS pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    breed VARCHAR(100) NOT NULL,
    age VARCHAR(50),
    adoption_status VARCHAR(20) DEFAULT 'available',
    species VARCHAR(20) NOT NULL,
    image TEXT
)");

/* ===============================
   INSERT SAMPLE DOG DATA
   =============================== */
$check = $conn->query("SELECT COUNT(*) AS count FROM pets WHERE species = 'dog'");
if ($check && $check->fetch_assoc()['count'] == 0) {

    $sql = "INSERT INTO pets (name, breed, age, adoption_status, species, image) VALUES
        ('Buddy', 'Golden Retriever', '3 years', 'available', 'dog', 'https://images.unsplash.com/photo-1601758125946-6ec2ef64daf8'),
        ('Lucy', 'Labrador', '4 years', 'available', 'dog', 'https://images.unsplash.com/photo-1583511655826-05700d52f4d9'),
        ('Charlie', 'German Shepherd', '2 years', 'adopted', 'dog', 'https://images.unsplash.com/photo-1560807707-8cc7568bd579'),
        ('Daisy', 'Beagle', '1 year', 'available', 'dog', 'https://images.unsplash.com/photo-1517849845537-4d257902454a'),
        ('Rocky', 'Bulldog', '5 years', 'pending', 'dog', 'https://images.unsplash.com/photo-1568554133264-7996ba14ca11'),
        ('Coco', 'Poodle', '2 years', 'available', 'dog', 'https://images.unsplash.com/photo-1543466835-00a7907ca9be')";

    $conn->query($sql);
}

/* ===============================
   FETCH DOGS
   =============================== */
$result = $conn->query("SELECT * FROM pets WHERE species = 'dog'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adopt a Dog</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dogs.css">
</head>
<body>

<div class="container">

    <div class="page-header">
        <h1 class="page-title">Adopt a Dog 🐶</h1>
        <a href="home.php" class="btn-adopt">Back to Home</a>
    </div>

    <div class="dog-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>

                <div class="dog-card">
                    <img src="<?= !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://place-puppy.com/400x300'; ?>"
                         alt="<?= htmlspecialchars($row['name']); ?>"
                         class="dog-image">

                    <div class="dog-details">
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
                                   onclick="return confirm('Are you sure?');">
                                   Delete
                                </a>

                            <?php elseif ($user_role === 'worker'): ?>
                                <a href="care_status.php?id=<?= $row['id']; ?>" class="btn-adopt">
                                    Update Care
                                </a>

                            <?php elseif ($status === 'available'): ?>
                                <a href="adoption_form.php?id=<?= $row['pet_id']; ?>" class="btn-adopt">Adopt Me</a>


                            <?php else: ?>
                                <button class="btn-adopt disabled" disabled>
                                    Not Available
                                </button>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <p>No dogs available right now.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
