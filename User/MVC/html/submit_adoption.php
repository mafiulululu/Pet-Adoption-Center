<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable exceptions for database errors
include '../db/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: home.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pet_id = isset($_POST['pet_id']) ? intval($_POST['pet_id']) : 0;
$back_link = isset($_POST['back_link']) ? $_POST['back_link'] : 'home.php';

// Fetch User Details
$sql_user = "SELECT full_name, email FROM users WHERE user_id = $user_id";
$result_user = $conn->query($sql_user);
if ($result_user->num_rows == 0) {
    die("User not found.");
}
$user_data = $result_user->fetch_assoc();

$phone = $conn->real_escape_string($_POST['phone']);
$address = $conn->real_escape_string($_POST['address']);
$full_name = $conn->real_escape_string($user_data['full_name']);
$email = $conn->real_escape_string($user_data['email']);

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

    // 3. Create Adoption Request Table if not exists
    $conn->query("CREATE TABLE IF NOT EXISTS adoption_requests (
        request_id INT AUTO_INCREMENT PRIMARY KEY,
        pet_id INT NOT NULL,
        user_id INT NOT NULL,
        full_name VARCHAR(100),
        email VARCHAR(100),
        phone VARCHAR(20),
        address TEXT,
        status VARCHAR(20) DEFAULT 'pending',
        request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 4. Insert Request
    $sql_request = "INSERT INTO adoption_requests ( user_id, full_name, email, phone, address, status,pet_id) VALUES ( $user_id, '$full_name', '$email', '$phone', '$address', 'pending',$pet_id)";
    $conn->query($sql_request);

    // 5. Update Pet Status
    $conn->query("UPDATE pets SET adoption_status = 'pending' WHERE id = $pet_id");

    $conn->commit();
    
    // Success Message and Redirect
    echo '<!DOCTYPE html>
    <html lang="en">
    <head><meta charset="UTF-8"><title>Success</title><link rel="stylesheet" href="../css/style.css"><meta http-equiv="refresh" content="3;url=my_requests.php"></head>
    <body><div class="container" style="text-align: center; padding-top: 5rem;">
        <div style="background: #d1fae5; color: #065f46; padding: 2rem; border-radius: 12px; display: inline-block;">
            <h2>✅ Request Submitted Successfully!</h2><p>You can now view and manage your request.</p><p>Redirecting to My Requests...</p>
        </div></div></body></html>';

} catch (Exception $e) {
    $conn->rollback();
    echo "Error submitting request: " . $e->getMessage();
}
?>