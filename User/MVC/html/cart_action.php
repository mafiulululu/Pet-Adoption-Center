<?php
session_start();
include '../db/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD ITEM
    if ($action === 'add') {
        $id = intval($_POST['id']);
        $type = $_POST['type']; // Table name (pet_food, pet_toys, etc.)
        
        // Validate table name to prevent SQL injection
        $allowed_tables = ['pet_food', 'pet_toys', 'pet_homes', 'pet_healthcare'];
        if (!in_array($type, $allowed_tables)) {
            die("Invalid item type");
        }

        // Fetch item details from database
        $stmt = $conn->prepare("SELECT * FROM $type WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $item = $result->fetch_assoc();
            $cartKey = $type . '_' . $id; // Unique key for session array

            if (isset($_SESSION['cart'][$cartKey])) {
                $_SESSION['cart'][$cartKey]['quantity']++;
            } else {
                $_SESSION['cart'][$cartKey] = [
                    'id' => $item['id'],
                    'type' => $type,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'image' => $item['image'],
                    'quantity' => 1
                ];
            }
        }
        header("Location: cart.php");
        exit();
    }

    // REMOVE ITEM
    if ($action === 'remove') {
        $key = $_POST['key'];
        if (isset($_SESSION['cart'][$key])) {
            unset($_SESSION['cart'][$key]);
        }
        header("Location: cart.php");
        exit();
    }

    // UPDATE QUANTITY
    if ($action === 'update') {
        $key = $_POST['key'];
        $qty = intval($_POST['quantity']);
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key]['quantity'] = max(1, $qty); // Ensure at least 1
        }
        header("Location: cart.php");
        exit();
    }
}
header("Location: home.php"); // Fallback
?>