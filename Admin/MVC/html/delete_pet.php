<?php
session_start();
include '../db/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM pets WHERE pet_id = $id");
}

header("Location: admin_dashboard.php");
exit();
?>