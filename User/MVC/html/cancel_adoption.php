<?php
session_start();
include '../db/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $request_id = intval($_POST['request_id']);
    $user_id = $_SESSION['user_id'];

    // Check if request exists and belongs to user
    $stmt = $conn->prepare("SELECT pet_id, status FROM adoption_requests WHERE request_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $request_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pet_id = $row['pet_id'];
        $status = $row['status'];

        if ($status === 'pending') {
            $conn->begin_transaction();
            try {
                // 1. Update Request Status to 'cancelled'
                $update_req = $conn->prepare("UPDATE adoption_requests SET status = 'cancelled' WHERE request_id = ?");
                $update_req->bind_param("i", $request_id);
                $update_req->execute();

                // 2. Update Pet Status back to 'available'
                $update_pet = $conn->prepare("UPDATE pets SET adoption_status = 'available' WHERE id = ?");
                $update_pet->bind_param("i", $pet_id);
                $update_pet->execute();

                $conn->commit();
                $_SESSION['message'] = "Adoption request cancelled successfully.";
            } catch (Exception $e) {
                $conn->rollback();
                $_SESSION['error'] = "Error cancelling request.";
            }
        } else {
            $_SESSION['error'] = "Cannot cancel a request that is not pending.";
        }
    } else {
        $_SESSION['error'] = "Request not found.";
    }
}

header("Location: my_requests.php");
exit();
?>