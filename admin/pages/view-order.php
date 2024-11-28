<?php
// Include the database connection
require_once '../config.php'; // Assuming config.php contains the database connection

// Check if order ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid order ID.");
}

$order_id = $_GET['id'];

// Fetch order details from the payments table
$order_query = "SELECT p.id, p.order_id, p.amount, p.payment_method, p.status, p.created_at, u.name AS user_name
                FROM payments p
                JOIN users u ON p.user_id = u.id
                WHERE p.id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

// Check if the order exists
if ($order_result->num_rows == 0) {
    die("Order not found.");
}

$order = $order_result->fetch_assoc();

// Fetch the order items
$order_items_query = "SELECT od.product_id, od.quantity, od.price, od.sub_total, pr.description, pr.image1
                      FROM order_details od
                      JOIN products pr ON od.product_id = pr.id
                      WHERE od.order_id = ?";
$stmt2 = $conn->prepare($order_items_query);
$stmt2->bind_param('i', $order['id']);
$stmt2->execute();
$order_items_result = $stmt2->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>SupplyEase - View Order</title>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-16 sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Order Details</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">View the details of your order.</p>
            </div>

            <div class="border-t border-gray-200">
                <dl class="divide-y divide-gray-200">
                    <!-- User Information -->
                    <div class="px-4 py-5 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Customer</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($order['user_name']); ?></dd>
                    </div>
                    <!-- Order Information -->
                    <div class="px-4 py-5 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Order ID</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($order['order_id']); ?></dd>
                    </div>
                    <div class="px-4 py-5 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                        <dd class="mt-1 text-sm text-gray-900">₱ <?= number_format($order['amount'], 2); ?></dd>
                    </div>
                    <div class="px-4 py-5 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($order['payment_method']); ?></dd>
                    </div>
                    <div class="px-4 py-5 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($order['status']); ?></dd>
                    </div>
                    <div class="px-4 py-5 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Date Ordered</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?= date('M d, Y', strtotime($order['created_at'])); ?></dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Order Items -->
        <div class="mt-10">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Items</h3>
            <div class="mt-5 bg-white shadow sm:rounded-lg">
                <ul role="list" class="divide-y divide-gray-200">
                    <?php while ($item = $order_items_result->fetch_assoc()) : ?>
                        <li class="px-4 py-5 sm:px-6">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <img src="http://146.190.85.108/admin/pages/<?= htmlspecialchars($item['image1']); ?>" alt="<?= htmlspecialchars($item['description']); ?>" class="h-20 w-20 object-cover rounded-md">
                                </div>
                                <div class="ml-6 flex-1">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item['description']); ?></div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <div class="text-sm text-gray-500">Quantity: <?= htmlspecialchars($item['quantity']); ?></div>
                                        <div class="text-sm text-gray-900">₱ <?= number_format($item['sub_total'], 2); ?></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="index.php?page=orders" class="border border-gray-400 px-5 py-3 rounded-lg text-sm/6 font-semibold text-gray-900">Back</a>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
