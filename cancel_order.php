<?php
// Start the session to access user session variables
session_start();

// Include database connection
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to cancel an order.'); window.location.href = 'login.php';</script>";
    exit();
}

// Get the order ID from the URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch the order and verify it belongs to the logged-in user
    $query = "SELECT * FROM payments WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $order_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the status in the payments table to 'Cancelled'
        $updateQuery = "UPDATE payments SET status = 'Cancelled' WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param('i', $order_id);

        if ($updateStmt->execute()) {
            // Fetch the products and quantities associated with the order
            $itemsQuery = "SELECT product_id, quantity FROM order_details WHERE order_id = ?";
            $itemsStmt = $conn->prepare($itemsQuery);
            $itemsStmt->bind_param('i', $order_id);
            $itemsStmt->execute();
            $itemsResult = $itemsStmt->get_result();

            // Update the stock for each product
            while ($item = $itemsResult->fetch_assoc()) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];

                $stockUpdateQuery = "UPDATE products SET stock = stock + ? WHERE id = ?";
                $stockStmt = $conn->prepare($stockUpdateQuery);
                $stockStmt->bind_param('ii', $quantity, $product_id);
                $stockStmt->execute();
            }

            echo "<script>alert('Order cancelled successfully. Stock has been updated.'); window.location.href = 'my-orders.php';</script>";
        } else {
            echo "<script>alert('Error cancelling the order. Please try again later.'); window.location.href = 'my-orders.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid order or permission denied.'); window.location.href = 'my-orders.php';</script>";
    }
} else {
    echo "<script>alert('Order ID not provided.'); window.location.href = 'my-orders.php';</script>";
}

$conn->close();
?>
