<?php
$host = "localhost";
$user = "root";        // change if needed
$pass = "";            // change if needed
$db   = "pet_adoption"; // your database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
