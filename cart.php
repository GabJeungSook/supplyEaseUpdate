<?php
// Start session and include database config
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to view your cart.'); window.location.href = 'login.php';</script>";
    exit();
}

// Get the user ID
$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user
$sql = "SELECT 
            ci.id AS cart_id, 
            ci.quantity, 
            ci.sub_total, 
            p.id AS product_id, 
            p.description AS product_name, 
            p.price AS product_price, 
            p.image1 AS product_image 
        FROM cart_items ci 
        JOIN products p ON ci.product_id = p.id 
        WHERE ci.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['sub_total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>SupplyEase - My Cart</title>
    <script>
        // Handle quantity change
        function updateQuantity(cartId, newQuantity) {
            if (newQuantity < 1) {
                alert('Quantity must be at least 1.');
                return;
            }
            fetch('update_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart_id: cartId, quantity: newQuantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh page to show updated values
                } else {
                    alert(data.message);
                }
            });
        }

        // Handle item removal
        function removeItem(cartId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('remove_cart_items.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ cart_id: cartId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Refresh page to remove item
                    } else {
                        alert(data.message);
                    }
                });
            }
        }
    </script>
</head>
<body>
<div class="bg-white">
    <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:px-0">
        <h1 class="text-center text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">My Cart</h1>

        <form class="mt-12">
            <section aria-labelledby="cart-heading">
                <h2 id="cart-heading" class="sr-only">Items in your shopping cart</h2>

                <ul role="list" class="divide-y divide-gray-200 border-b border-t border-gray-200">
                    <?php foreach ($cart_items as $item): ?>
                        <li class="flex py-6">
                            <div class="shrink-0">
                                <img src="http://146.190.85.108/admin/pages/<?php echo htmlspecialchars($item['product_image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="size-24 rounded-md object-cover sm:size-32">
                            </div>
                            <div class="ml-4 flex flex-1 flex-col sm:ml-6">
                                <div>
                                    <div class="flex justify-between">
                                        <h4 class="text-sm">
                                            <a href="#" class="font-medium text-gray-700 hover:text-gray-800"><?php echo htmlspecialchars($item['product_name']); ?></a>
                                        </h4>
                                        <p class="ml-4 text-sm font-medium text-gray-900">₱ <?php echo number_format($item['product_price'] * $item['quantity'] , 2); ?></p>
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-1 items-end justify-between">
                                    <div>
                                        <label for="quantity-<?php echo $item['cart_id']; ?>">Quantity: </label>
                                        <input type="number" id="quantity-<?php echo $item['cart_id']; ?>" class="p-3 w-16 rounded-md border border-gray-400 text-sm text-gray-900" value="<?php echo $item['quantity']; ?>" min="1" onchange="updateQuantity(<?php echo $item['cart_id']; ?>, this.value)">
                                    </div>
                                    <div class="ml-4">
                                        <button type="button" class="text-sm font-medium text-indigo-600 hover:text-indigo-500" onclick="removeItem(<?php echo $item['cart_id']; ?>)">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <!-- Order summary -->
            <section aria-labelledby="summary-heading" class="mt-10">
                <h2 id="summary-heading" class="sr-only">Order summary</h2>

                <div>
                    <dl class="space-y-4">
                        <div class="flex items-center justify-between">
                            <dt class="text-base font-medium text-gray-900">Subtotal</dt>
                            <dd class="ml-4 text-base font-medium text-gray-900">₱ <?php echo number_format($total, 2); ?></dd>
                        </div>
                    </dl>
                    <!-- <p class="mt-1 text-sm text-gray-500">Shipping and taxes will be calculated at checkout.</p> -->
                </div>

            <div class="mt-10">
                <div id="paypal-button-container"></div>
            </div>
            <div class="mt-4">
                <button type="button" class="w-full rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        onclick="handleCOD()">Cash on Delivery</button>
            </div>
            <div class="mt-6 text-center text-sm">
                <p>
                    or
                    <a href="index.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Continue Shopping
                    <span aria-hidden="true"> &rarr;</span>
                    </a>
                </p>
            </div>
            </section>
        </form>
    </div>
</div>
<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=Ad7m8uGJ-XKGCMLOVkqzARALsoER799iqaW5VKJXfrfNZOuOspUuwc6nfbb_ufNCZ84z_IEVavc2JyYN&currency=PHP"></script>

<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo $total; ?>', // Cart total in PHP
                        currency_code: "PHP"
                    },
                }]
            });
        },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            const orderDetails = <?php echo json_encode($cart_items); ?>;
            const totalAmount = '<?php echo $total; ?>';
            // Redirect to payment processing script
            const formData = new FormData();
            formData.append('orderID', data.orderID);
            formData.append('payerID', data.payerID);
            formData.append('paymentDetails', JSON.stringify(details));
            formData.append('orderDetails', JSON.stringify(orderDetails)); // Add order items
            formData.append('totalAmount', totalAmount); // Include the total amount
            formData.append('payment_method', 'PAYPAL');
            console.log(formData);
            fetch('process_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment completed successfully!');
                    window.location.href = 'payment_success.php';
                } else {
                    alert('Payment failed: ' + data.message);
                }
            });
        });
    },
    onCancel: function(data) {
        alert('Payment canceled.');
    },
    onError: function(err) {
        console.error(err);
        alert('An error occurred during the transaction.');
    }
}).render('#paypal-button-container');


</script>
<script>
//cod
function handleCOD() {
    // Fetch order details and total amount from PHP variables
    const orderDetails = <?php echo json_encode($cart_items); ?>;
    const totalAmount = <?php echo $total; ?>;

    // Confirm action with the user
    if (confirm('Are you sure you want to place an order with Cash on Delivery?')) {
        // Create FormData to send to the server
        const formData = new FormData();
        formData.append('orderID', 'COD-' + Date.now()); // Generate unique order ID
        formData.append('payerID', 'COD-' + Date.now()); // Generate unique payer ID
        //formData.append('paymentStatus', 'COD'); // Add payment details
        formData.append('orderDetails', JSON.stringify(orderDetails)); // Add order items
        formData.append('totalAmount', totalAmount); // Include the total amount
        formData.append('payment_method', 'COD'); // Specify payment method

        // Send data to process_payment.php via POST
        fetch('process_payment_cod.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // If the server responds with success
                    alert('Order placed successfully with Cash on Delivery!');
                    window.location.href = 'payment_success.php'; // Redirect to success page
                } else {
                    // If the server responds with an error
                    alert('Order placement failed: ' + data.message);
                }
            })
            .catch(err => {
                // Catch and log any errors
                console.error(err);
                alert('An error occurred while placing your order.'+ err);
            });
    }
}
</script>
</body>
</html>
