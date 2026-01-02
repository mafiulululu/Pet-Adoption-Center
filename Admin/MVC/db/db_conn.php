<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "pet-adoption-center";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connect Error: " . $conn->connect_error);
}
?>
