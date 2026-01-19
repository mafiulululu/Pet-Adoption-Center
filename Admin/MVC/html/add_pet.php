<?php
session_start();
include '../db/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $image = $_POST['image'];
    $description = $_POST['description'];
    $admin_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO pets (name, species, breed, age, image, description, added_by, adoption_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'available')");
    $stmt->bind_param("sssisii", $name, $species, $breed, $age, $image, $description, $admin_id);
    
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Error adding pet.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Pet - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/home.css">
    <style>
        .form-container { max-width: 600px; margin: 4rem auto; padding: 2rem; background: white; border-radius: 16px; }
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        .form-input { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Add New Pet</h2>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Pet Name</label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Species (cat, dog, rabbit, tortoise)</label>
                    <select name="species" class="form-input">
                        <option value="cat">Cat</option>
                        <option value="dog">Dog</option>
                        <option value="rabbit">Rabbit</option>
                        <option value="tortoise">Tortoise</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Breed</label>
                    <input type="text" name="breed" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Age (years)</label>
                    <input type="number" name="age" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Image URL</label>
                    <input type="url" name="image" class="form-input" placeholder="https://example.com/image.jpg">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-input" rows="4"></textarea>
                </div>
                <button type="submit" class="btn-primary">Add Pet</button>
                <a href="admin_dashboard.php" style="display:block; text-align:center; margin-top:1rem;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>