<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Initialize message variable
$message = "";

// Handle cancel order request
if (isset($_GET['cancel_order_id'])) {
    $order_id = intval($_GET['cancel_order_id']);
    $user_id = $_SESSION['user_id'];

    // Check if the order belongs to the user and its current status
    $sql = "SELECT status FROM payments WHERE order_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if ($order) {
        if ($order['status'] === 'To Receive') {
            // Update the status to "Cancelled"
            $sql = "UPDATE payments SET status = 'Cancelled' WHERE order_id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $order_id, $user_id);
            if ($stmt->execute()) {
                $message = "Order #$order_id has been successfully cancelled.";
            } else {
                $message = "Failed to cancel order #$order_id. Please try again.";
            }
            $stmt->close();
        } elseif ($order['status'] === 'Completed') {
            $message = "Order #$order_id cannot be cancelled as it is already completed.";
        } else {
            $message = "Order #$order_id cannot be cancelled.";
        }
    } else {
        $message = "Invalid order ID or you do not have permission to cancel this order.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Order</title>
</head>
<body>
    <h1>Cancel Order</h1>

    <!-- Display the success or error message -->
    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
        <a href="my-orders.php">Go back to Orders</a> <!-- Add navigation to orders page -->
    <?php endif; ?>
</body>
</html>
