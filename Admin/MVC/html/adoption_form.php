<?php
session_start();
include '../db/db_conn.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?tab=login");
    exit();
}

$pet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);

    // Start Transaction
    $conn->begin_transaction();

    try {
        // 1. Update User Contact Info
        $conn->query("UPDATE users SET phone_number = '$phone' WHERE user_id = $user_id");

        // 2. Update/Insert Address in User Profile
        $check_profile = $conn->query("SELECT profile_id FROM user_profiles WHERE user_id = $user_id");
        if ($check_profile->num_rows > 0) {
            $conn->query("UPDATE user_profiles SET address = '$address' WHERE user_id = $user_id");
        } else {
            $conn->query("INSERT INTO user_profiles (user_id, address) VALUES ($user_id, '$address')");
        }

        // 3. Create Adoption Request
        $conn->query("INSERT INTO adoption_requests (pet_id, client_id, status) VALUES ($pet_id, $user_id, 'pending')");

        // 4. Update Pet Status
        $conn->query("UPDATE pets SET adoption_status = 'pending' WHERE pet_id = $pet_id");

        $conn->commit();
        $message = "Adoption request submitted successfully! We will contact you soon.";
        $messageType = "success";
        
        // Redirect to cats page after 3 seconds
        header("refresh:3;url=cats.php");

    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error submitting request. Please try again.";
        $messageType = "error";
    }
}

// Fetch Pet Details
$sql_pet = "SELECT * FROM pets WHERE pet_id = $pet_id";
$result_pet = $conn->query($sql_pet);

if ($result_pet->num_rows == 0) {
    echo "Pet not found.";
    exit();
}
$pet = $result_pet->fetch_assoc();

// Fetch User Details for Pre-filling
$sql_user = "SELECT u.full_name, u.email, u.phone_number, p.address 
             FROM users u 
             LEFT JOIN user_profiles p ON u.user_id = p.user_id 
             WHERE u.user_id = $user_id";
$result_user = $conn->query($sql_user);
$user = $result_user->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopt <?php echo htmlspecialchars($pet['name']); ?> - Pet Adoption Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .adopt-container {
            max-width: 900px;
            margin: 3rem auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
        }
        .pet-preview {
            flex: 1;
            min-width: 300px;
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            padding: 2rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .pet-preview img {
            width: 100%;
            max-width: 280px;
            height: 280px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .form-section {
            flex: 1.5;
            padding: 3rem;
            min-width: 350px;
        }
        .form-title { font-size: 1.8rem; margin-bottom: 0.5rem; color: #1a1a2e; }
        .form-subtitle { color: #666; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #333; }
        .form-input { width: 100%; padding: 0.8rem; border: 2px solid #eee; border-radius: 8px; font-family: 'Inter', sans-serif; }
        .form-input:focus { border-color: #FF1B6B; outline: none; }
        .btn-submit { width: 100%; padding: 1rem; background: linear-gradient(135deg, #FF1B6B, #45CAFF); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 1rem; transition: transform 0.2s; }
        .btn-submit:hover { transform: translateY(-2px); }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; }
        .alert.success { background: #d4edda; color: #155724; }
        .alert.error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <div class="adopt-container">
        <div class="pet-preview">
            <img src="<?php echo !empty($pet['image']) ? htmlspecialchars($pet['image']) : 'https://placekitten.com/400/300'; ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
            <h2><?php echo htmlspecialchars($pet['name']); ?></h2>
            <p style="color: #666; margin-top: 0.5rem;">
                <?php echo htmlspecialchars($pet['breed']); ?> • <?php echo htmlspecialchars($pet['age']); ?> years old
            </p>
        </div>

        <div class="form-section">
            <h1 class="form-title">Adoption Application</h1>
            <p class="form-subtitle">Complete the form below to request adoption.</p>

            <?php if ($message): ?>
                <div class="alert <?php echo $messageType; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($pet['adoption_status'] === 'available' && empty($message)): ?>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-input" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly style="background: #f9f9f9;">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="background: #f9f9f9;">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-input" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" required placeholder="e.g. +1 234 567 8900">
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-input" rows="3" required placeholder="Enter your full address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn-submit">Submit Application</button>
                <a href="cats.php" style="display: block; text-align: center; margin-top: 1rem; color: #666; text-decoration: none;">Cancel</a>
            </form>
            <?php elseif ($pet['adoption_status'] !== 'available' && empty($message)): ?>
                <div class="alert error">
                    This pet is currently <strong><?php echo ucfirst($pet['adoption_status']); ?></strong> and cannot be adopted.
                </div>
                <a href="cats.php" class="btn-submit" style="text-align: center; display: block; text-decoration: none;">Back to Cats</a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>