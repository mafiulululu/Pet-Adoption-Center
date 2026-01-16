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

// Fetch Pet Details
$sql_pet = "SELECT * FROM pets WHERE pet_id = $pet_id";
$result_pet = $conn->query($sql_pet);

if ($result_pet->num_rows == 0) {
    echo "Pet not found.";
    exit();
}
$pet = $result_pet->fetch_assoc();

// Determine back link based on pet type/species
$back_link = 'cats.php'; // Default
if (isset($pet['type'])) {
    $t = strtolower($pet['type']);
    if ($t === 'dog') $back_link = 'dogs.php';
    if ($t === 'rabbit') $back_link = 'rabbits.php';
    if ($t === 'tortoise') $back_link = 'tortoises.php';
} elseif (isset($pet['species']) && strtolower($pet['species']) === 'cat') {
    $back_link = 'cats.php';
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $full_name = $conn->real_escape_string($user['full_name']);
    $email = $conn->real_escape_string($user['email']);

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
        $sql_request = "INSERT INTO adoption_requests (pet_id, user_id, full_name, email, phone, address, status) VALUES ($pet_id, $user_id, '$full_name', '$email', '$phone', '$address', 'pending')";
        $conn->query($sql_request);

        // 4. Update Pet Status
        $conn->query("UPDATE pets SET adoption_status = 'pending' WHERE pet_id = $pet_id");

        $conn->commit();
        $message = "Adoption request submitted successfully! We will contact you soon.";
        $messageType = "success";
        
        // Redirect to cats page after 3 seconds
        header("refresh:3;url=$back_link");

    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error submitting request. Please try again.";
        $messageType = "error";
    }
}

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
    <link rel="stylesheet" href="../css/home.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Page Layout Overrides */
        body {
            display: block; /* Override potential flex from other css */
            background: var(--bg-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Adoption Section */
        .adopt-section {
            padding: 4rem 0;
            flex: 1;
        }
        
        .adopt-card {
            background: var(--bg-secondary);
            border-radius: 24px;
            box-shadow: 0 20px 60px var(--shadow);
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
            max-width: 1000px;
            margin: 0 auto;
            border: 1px solid var(--border-color);
        }
        
        .pet-preview {
            flex: 1;
            min-width: 350px;
            background: linear-gradient(135deg, #FF1B6B 0%, #C71585 50%, #45CAFF 100%);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            position: relative;
        }
        
        .pet-preview::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.3;
        }
        
        .pet-image {
            width: 250px;
            height: 250px;
            object-fit: cover;
            border-radius: 50%;
            border: 6px solid rgba(255,255,255,0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 2rem;
            z-index: 1;
            background-color: #fff;
        }
        
        .pet-info {
            z-index: 1;
        }
        
        .pet-name {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: white;
        }
        
        .pet-meta {
            font-size: 1.1rem;
            opacity: 0.95;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            display: inline-block;
            margin-top: 0.5rem;
            color: white;
            font-weight: 500;
        }
        
        .form-section {
            flex: 1.5;
            padding: 3rem;
            min-width: 350px;
        }
        
        .form-title { 
            font-size: 2rem; 
            font-weight: 700;
            margin-bottom: 0.5rem; 
            color: var(--gray-900); 
        }
        
        .form-subtitle { 
            color: var(--gray-600); 
            margin-bottom: 2rem; 
            font-size: 1.1rem;
        }
        
        .form-group { margin-bottom: 1.5rem; }
        
        .form-label { 
            display: block; 
            font-weight: 600; 
            margin-bottom: 0.5rem; 
            color: var(--gray-700); 
        }
        
        .form-input { 
            width: 100%; 
            padding: 1rem; 
            border: 2px solid var(--gray-200); 
            border-radius: 12px; 
            font-family: 'Inter', sans-serif; 
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--bg-primary);
            color: var(--text-primary);
        }
        
        .form-input:focus { 
            border-color: var(--primary); 
            outline: none; 
            box-shadow: 0 0 0 4px rgba(255, 27, 107, 0.1);
        }
        
        .form-input[readonly] {
            background-color: var(--gray-100);
            cursor: not-allowed;
        }
        
        .btn-submit { 
            width: 100%; 
            padding: 1rem; 
            background: linear-gradient(135deg, #FF1B6B 0%, #45CAFF 100%); 
            color: white; 
            border: none; 
            border-radius: 12px; 
            font-weight: 700; 
            cursor: pointer; 
            font-size: 1.1rem; 
            transition: transform 0.2s, box-shadow 0.2s; 
            margin-top: 1rem;
        }
        
        .btn-submit:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 10px 20px rgba(255, 27, 107, 0.3);
        }
        
        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--gray-500);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        
        .btn-cancel:hover { color: var(--primary); }
        
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; }
        .alert.success { background: #d4edda; color: #155724; }
        .alert.error { background: #f8d7da; color: #721c24; }
        
        @media (max-width: 768px) {
            .adopt-card { flex-direction: column; }
            .pet-preview { padding: 3rem 2rem; }
            .form-section { padding: 2rem; }
        }
    </style>
</head>
<body>

    <!-- Navigation Header (From Home) -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <svg class="brand-icon" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="60" cy="70" rx="18" ry="22" fill="url(#nav-gradient)" />
                    <ellipse cx="45" cy="45" rx="12" ry="15" fill="url(#nav-gradient)" />
                    <ellipse cx="75" cy="45" rx="12" ry="15" fill="url(#nav-gradient)" />
                    <ellipse cx="35" cy="55" rx="10" ry="13" fill="url(#nav-gradient)" />
                    <ellipse cx="85" cy="55" rx="10" ry="13" fill="url(#nav-gradient)" />
                    <defs>
                        <linearGradient id="nav-gradient" x1="0" y1="0" x2="120" y2="120">
                            <stop offset="0%" stop-color="#FF1B6B" />
                            <stop offset="100%" stop-color="#45CAFF" />
                        </linearGradient>
                    </defs>
                </svg>
                <span class="brand-name">Pet Adoption Center</span>
            </div>

            <ul class="nav-menu">
                <li class="nav-item"><a href="home.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="home.php#about" class="nav-link">About Us</a></li>
                <li class="nav-item dropdown">
                    <a href="home.php#categories" class="nav-link">Categories ▾</a>
                    <ul class="dropdown-menu">
                        <li><a href="cats.php">🐱 Cats</a></li>
                        <li><a href="dogs.php">🐕 Dogs</a></li>
                        <li><a href="rabbits.php">🐇 Rabbits</a></li>
                        <li><a href="tortoises.php">🐢 Tortoises</a></li>
                        <li><a href="pet_food.php">Pet Food</a></li>
                        <li><a href="pet_toys.php">Pet Toys</a></li>
                        <li><a href="pet_homes.php">Pet Homes</a></li>
                        <li><a href="pet_healthcare.php">Pet Healthcare</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a href="home.php#shop" class="nav-link">Shop</a></li>
            </ul>

            <div class="nav-actions">
                <?php if (isset($_SESSION['user_name'])): ?>
                    <button class="btn-secondary" onclick="alert('Logged in as: <?php echo htmlspecialchars($_SESSION['user_name']); ?>')">My Profile</button>
                <?php endif; ?>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <section class="adopt-section">
    <div class="container">
    <div class="adopt-card">
        <div class="pet-preview">
            <img src="<?php echo !empty($pet['image']) ? htmlspecialchars($pet['image']) : 'https://placekitten.com/400/300'; ?>" 
                 alt="<?php echo htmlspecialchars($pet['name']); ?>" 
                 class="pet-image">
            
            <div class="pet-info">
                <h2 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h2>
                <div class="pet-meta">
                    <?php echo htmlspecialchars($pet['breed']); ?> • <?php echo htmlspecialchars($pet['age']); ?> years old
                </div>
            </div>
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
                    <input type="text" class="form-input" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
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
                <a href="<?php echo $back_link; ?>" class="btn-cancel">Cancel</a>
            </form>
            <?php elseif ($pet['adoption_status'] !== 'available' && empty($message)): ?>
                <div class="alert error">
                    This pet is currently <strong><?php echo ucfirst($pet['adoption_status']); ?></strong> and cannot be adopted.
                </div>
                <a href="<?php echo $back_link; ?>" class="btn-submit" style="text-align: center; display: block; text-decoration: none; line-height: 1.5;">Back to List</a>
            <?php endif; ?>
        </div>
    </div>
    </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4 class="footer-title">Pet Adoption Center</h4>
                    <p class="footer-text">Connecting pets with loving families since 2015</p>
                </div>
                <div class="footer-section">
                    <h4 class="footer-title">Contact</h4>
                    <p class="footer-text">Email: info@petadoption.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Pet Adoption Center. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>