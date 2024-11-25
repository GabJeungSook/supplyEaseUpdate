<?php
session_start();
include('config.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

// Get input data
$data = json_decode(file_get_contents('php://input'), true);
$cart_id = $data['cart_id'];

// Check if the cart item exists
$sql = "SELECT id FROM cart_items WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $cart_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found.']);
    exit();
}

// Delete the cart item
$delete_sql = "DELETE FROM cart_items WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param('i', $cart_id);

if ($delete_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Item removed from cart.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart.']);
}
