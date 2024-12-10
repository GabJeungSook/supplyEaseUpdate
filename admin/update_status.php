<?php
// Include the database connection
require_once '../config.php';

// Set the Content-Type header for JSON response
header('Content-Type: application/json');

// Read the JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Check if the data contains order_id and new_status
if (isset($data['order_id']) && isset($data['new_status'])) {
    $orderId = intval($data['order_id']);
    $newStatus = $data['new_status'];

    // Update the order status
    $stmt = $conn->prepare("UPDATE payments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $orderId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input: order_id or new_status not provided.']);
}

$conn->close();
exit();
