<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve PayPal data
    $orderID = $_POST['orderID'];
    $payerID = $_POST['payerID'];
    $paymentStatus = $_POST['paymentStatus'];
    $orderDetails = json_decode($_POST['orderDetails'], true); // Get order items (cart details)
    $totalAmount = $_POST['totalAmount']; // The total amount of the payment
    $payment_method = $_POST['payment_method'];

        $user_id = $_SESSION['user_id'];

        // Insert payment details into the payments table
        $stmt = $conn->prepare("
            INSERT INTO payments (user_id, order_id, payer_id, payment_status, amount, payment_method) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            'isssds',
            $user_id,
            $orderID,
            $payerID,
            $paymentStatus,
            $totalAmount,
            $payment_method
        );
        $stmt->execute();
        $paymentID = $stmt->insert_id; // Get the last inserted payment ID

        // Insert order details (products in the cart) and update stock
        foreach ($orderDetails as $item) {
            $productID = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['product_price'];
            $subTotal = $item['sub_total'];

            // Insert into order_details table
            $stmt2 = $conn->prepare("
                INSERT INTO order_details (order_id, product_id, quantity, price, sub_total) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt2->bind_param('iiidd', $paymentID, $productID, $quantity, $price, $subTotal);
            $stmt2->execute();

            // Update the product stock in the products table
            $stmt3 = $conn->prepare("
                UPDATE products 
                SET stock = stock - ? 
                WHERE id = ?
            ");
            $stmt3->bind_param('ii', $quantity, $productID);
            $stmt3->execute();
        }

        // Clear the user's cart after successful payment
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();

        // Return success response
        echo json_encode(['success' => true]);
} else {
    http_response_code(405); // Method not allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
