<?php
session_start();
include('config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $order_id = $data['order_id'] ?? null;

    if ($order_id) {
        $update_query = "UPDATE payments SET status = 'Completed' WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('ii', $order_id, $_SESSION['user_id']);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
