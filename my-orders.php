<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Query to fetch user data from `users` and `user_details` tables
$users_query = "
    SELECT 
        users.name, 
        users.email, 
        user_details.full_address, 
        user_details.contact_number, 
        user_details.gender, 
        user_details.birthday
    FROM users
    INNER JOIN user_details ON users.id = user_details.user_id
    WHERE users.id = ?
";

$stmt = $conn->prepare($users_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Fetch the orders and order details from the database
$orders_query = "SELECT p.id, p.order_id, p.payment_status, p.amount, p.created_at, p.payment_method, p.status
                 FROM payments p
                 WHERE p.user_id = ? 
                 ORDER BY p.created_at DESC";

$stmt = $conn->prepare($orders_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();

// Fetch order items for each order
$order_items_query = "SELECT od.product_id, od.quantity, od.price, od.sub_total, pr.description, pr.image1
                      FROM order_details od
                      JOIN products pr ON od.product_id = pr.id
                      WHERE od.order_id = ?";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>SupplyEase - My Orders</title>
</head>
<body>
<a href="index.php" class="p-3 inline-block mb-6 bg-green-900 rounded-lg m-3 text-gray-50 hover:text-gray-100">
    &larr; Back to Home
</a>
<div class="bg-white">
<div class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl sm:px-2 lg:px-8">
            <div class="mx-auto max-w-2xl px-4 lg:max-w-4xl lg:px-0">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl bg-gray-200">My Profile</h1>
            </div>
        </div>

        <div class="mt-16">
            <div class="mx-auto max-w-7xl sm:px-2 lg:px-8">
                <div class="mx-auto max-w-2xl space-y-8 sm:px-4 lg:max-w-4xl lg:px-0">
                    <div class="p-4 border-b border-t border-gray-500 bg-whitesmoke-500 shadow-sm sm:rounded-lg sm:border">
                           <!-- profile content -->
                           <div>
    <div class="mt-6 border-t border-gray-100">
        <dl class="divide-y divide-gray-100">
            <!-- Full Name -->
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium text-gray-900">Full name</dt>
                <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0"><?php echo htmlspecialchars($userData['name']); ?></dd>
            </div>
            <!-- Birthday -->
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium text-gray-900">Birthday</dt>
                <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0"> <?php echo htmlspecialchars(date('F d, Y', strtotime($userData['birthday']))); ?></dd>
            </div>
            <!-- Email Address -->
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium text-gray-900">Email address</dt>
                <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0"><?php echo htmlspecialchars($userData['email']); ?></dd>
            </div>
            <!-- Gender -->
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium text-gray-900">Gender</dt>
                <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0"><?php echo htmlspecialchars($userData['gender']); ?></dd>
            </div>
            <!-- Contact Number -->
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium text-gray-900">Contact Number</dt>
                <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0"><?php echo htmlspecialchars($userData['contact_number']); ?></dd>
            </div>
            <!-- About -->
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium text-gray-900">Full Address</dt>
                <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0"><?php echo htmlspecialchars($userData['full_address']); ?>
                </dd>
            </div>
        </dl>
    </div>
</div>


                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl sm:px-2 lg:px-8">
            <div class="mx-auto max-w-2xl px-4 lg:max-w-4xl lg:px-0">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">My Orders</h1>
                <!-- <p class="mt-2 text-sm text-gray-500">Check the status of recent orders, manage returns, and discover similar products.</p> -->
            </div>
        </div>

        <div class="mt-16">
            <h2 class="sr-only">Recent orders</h2>
            <div class="mx-auto max-w-7xl sm:px-2 lg:px-8">
                <div class="mx-auto max-w-2xl space-y-8 sm:px-4 lg:max-w-4xl lg:px-0">
                    <?php while ($order = $orders_result->fetch_assoc()) : ?>
                        <div class="border-b border-t border-gray-200 bg-white shadow-sm sm:rounded-lg sm:border">
                            <h3 class="sr-only">Order placed on <?= date('M d, Y', strtotime($order['created_at'])); ?></h3>
                            <div class="flex items-center border-b border-gray-200 p-4 sm:grid sm:grid-cols-4 sm:gap-x-6 sm:p-6">
                                <dl class="grid flex-1 grid-cols-5 gap-x-6 text-sm sm:col-span-3 sm:grid-cols-5 lg:col-span-5">
                                    <div>
                                        <dt class="font-medium text-gray-900">Order number</dt>
                                        <dd class="mt-1 text-gray-500"><?= $order['order_id']; ?></dd>
                                    </div>
                                    <div class="hidden sm:block">
                                        <dt class="font-medium text-gray-900">Date placed</dt>
                                        <dd class="mt-1 text-gray-500">
                                            <time datetime="<?= $order['created_at']; ?>"><?= date('M d, Y', strtotime($order['created_at'])); ?></time>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-900">Total amount</dt>
                                        <dd class="mt-1 font-medium text-gray-900">₱ <?= number_format($order['amount'], 2); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-900">Payment Method</dt>
                                        <dd class="mt-1 font-medium text-gray-900"><?= $order['payment_method']; ?></dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-900">Order Status</dt>
                                        <dd class="mt-1 font-medium text-gray-900"><?= htmlspecialchars($order['status']); ?></dd>

                                        <?php if ($order['status'] === 'To Receive') : ?>
                                            <button 
                                                onclick="updateStatus(<?= $order['id']; ?>)" 
                                                class="mt-2 whitespace-nowrap text-green-600 hover:text-green-500">
                                                Complete Order
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </dl>
                            </div>

                            <!-- Products -->
                            <h4 class="sr-only">Items</h4>
                            <ul role="list" class="divide-y divide-gray-200">
                                <?php
                                $stmt2 = $conn->prepare($order_items_query);
                                $stmt2->bind_param('i', $order['id']);
                                $stmt2->execute();
                                $order_items_result = $stmt2->get_result();

                                while ($item = $order_items_result->fetch_assoc()) :
                                ?>
                                    <li class="p-4 sm:p-6">
                                        <div class="flex items-center sm:items-start">
                                            <div class="size-20 shrink-0 overflow-hidden rounded-lg bg-gray-200 sm:size-40">
                                                <img src="http://146.190.85.108/admin/pages/<?= $item['image1']; ?>" alt="<?= $item['description']; ?>" class="size-full object-cover">
                                            </div>
                                           

                                            <div class="ml-6 flex-1 text-sm">
                                                <div class="font-medium text-gray-900 sm:flex sm:justify-between">
                                                    <h5><?= $item['description']; ?></h5>
                                                    <p class="mt-2 sm:mt-0">₱ <?= number_format($item['price'] * $item['quantity'], 2); ?></p>
                                                </div>
                                                <p class="hidden text-gray-500 sm:mt-2 sm:block">₱ <?= $item['sub_total']; ?></p>
                                            </div>
                                        </div>

                                        <div class="mt-6 sm:flex sm:justify-between">
                                            <div class="flex items-center">
                                                <p class="ml-2 text-sm font-medium text-gray-500">Quantity: <?= $item['quantity']; ?></p>
                                            </div>
                                            <div class="mt-6 flex items-center space-x-4 divide-x divide-gray-200 border-t border-gray-200 pt-4 text-sm font-medium sm:ml-4 sm:mt-0 sm:border-none sm:pt-0">
                                                <div class="flex flex-1 justify-center">
                                                    <a href="product_details.php?id=<?= $item['product_id']; ?>" class="whitespace-nowrap text-indigo-600 hover:text-indigo-500">View product</a>
                                                </div>
                                                <div class="flex flex-1 justify-center pl-4">
                                                    <!-- You can add additional options here (e.g., cancel, return) -->
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function updateStatus(orderId) {
        if (confirm("Are you sure you want to update the status to Completed?")) {
            fetch('update-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ order_id: orderId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Status updated successfully.");
                    location.reload(); // Refresh the page
                } else {
                    alert("Failed to update status: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while updating the status.");
            });
        }
    }
</script>


</body>
</html>
