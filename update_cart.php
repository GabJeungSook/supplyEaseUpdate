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
$new_quantity = (int) $data['quantity'];

if ($new_quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1.']);
    exit();
}

// Check if the cart item exists
$sql = "SELECT ci.id, p.price FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.id = ? AND ci.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $cart_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found.']);
    exit();
}

$row = $result->fetch_assoc();
$product_price = $row['price'];
$new_sub_total = $new_quantity * $product_price;

// Update cart item
$update_sql = "UPDATE cart_items SET quantity = ?, sub_total = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param('idi', $new_quantity, $new_sub_total, $cart_id);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cart updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update cart.']);
}
