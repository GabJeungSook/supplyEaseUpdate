<?php
// Start session to track user data
session_start();

// Include database configuration
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to add items to the cart.'); window.location.href = 'login.php';</script>";
    exit();
}

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Check if the product_id and quantity are provided via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Validate inputs
    if ($product_id <= 0 || $quantity <= 0) {
        echo "<script>alert('Invalid product ID or quantity.'); window.history.back();</script>";
        exit();
    }

    // Fetch the product price from the database
    $sql = "SELECT price FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo "<script>alert('Product not found.'); window.history.back();</script>";
        exit();
    }

    $product_price = $product['price'];
    $sub_total = $product_price * $quantity;

    // Check if the product already exists in the cart for this user
    $sql_check = "SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param('ii', $user_id, $product_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $cart_item = $result_check->fetch_assoc();

    if ($cart_item) {
        // Update the existing cart item
        $new_quantity = $cart_item['quantity'] + $quantity;
        $new_sub_total = $product_price * $new_quantity;

        $sql_update = "UPDATE cart_items SET quantity = ?, sub_total = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param('idi', $new_quantity, $new_sub_total, $cart_item['id']);
        $stmt_update->execute();

        echo "<script>alert('Cart updated successfully.'); window.location.href = 'index.php';</script>";
    } else {
        // Insert a new cart item
        $sql_insert = "INSERT INTO cart_items (user_id, product_id, quantity, sub_total) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param('iiid', $user_id, $product_id, $quantity, $sub_total);
        $stmt_insert->execute();

        echo "<script>alert('Product added to cart successfully.'); window.location.href = 'index.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
    exit();
}
?>
