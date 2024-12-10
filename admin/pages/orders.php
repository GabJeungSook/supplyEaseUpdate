<?php
// Include the database connection
require_once '../config.php'; // Assuming config.php contains the database connection

// Get the status filter from the URL or default to 'all'
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Base SQL query
$sql = "SELECT p.id, p.order_id, p.amount, p.payment_method, p.shipping_address, p.status, p.created_at, u.name AS user_name
        FROM payments p
        JOIN users u ON p.user_id = u.id";

// Modify the SQL query based on the filter
if ($statusFilter === 'received') {
    $sql .= " WHERE p.status = 'received'";
} elseif ($statusFilter === 'completed') {
    $sql .= " WHERE p.status = 'completed'";
} elseif ($statusFilter === 'place_order') {
    $sql .= " WHERE p.status = 'place_order'";
} elseif ($statusFilter === 'preparing') {
    $sql .= " WHERE p.status = 'preparing'";
} elseif ($statusFilter === 'out_for_delivery') {
    $sql .= " WHERE p.status = 'out_for_delivery'";
}

// Add sorting by creation date in descending order
$sql .= " ORDER BY p.created_at DESC";

// Execute the query
$result = $conn->query($sql);

// Check for errors
if ($result === false) {
    die("Error fetching orders: " . $conn->error);
}
?>

<div>
    <div class="text-2xl font-semibold">
        <span>Orders</span>
    </div>
    <form method="GET" action="index.php?page=orders">
        <label for="status">Status:</label>
        <select name="status" id="status" onchange="this.form.submit()">
            <option value="all" <?php echo ($statusFilter === 'all') ? 'selected' : ''; ?>>All Orders</option>
            <option value="Order Placed" <?php echo ($statusFilter === 'Order Placed') ? 'selected' : ''; ?>>Order Placed</option>
            <option value="preparing" <?php echo ($statusFilter === 'preparing') ? 'selected' : ''; ?>>Preparing</option>
            <option value="out_for_delivery" <?php echo ($statusFilter === 'out_for_delivery') ? 'selected' : ''; ?>>Out for Delivery</option>
            <option value="received" <?php echo ($statusFilter === 'received') ? 'selected' : ''; ?>>Received Orders</option>
            <option value="completed" <?php echo ($statusFilter === 'completed') ? 'selected' : ''; ?>>Cancelled Orders</option>
        </select>
    </form>

    <div class="mt-10 px-4 sm:px-6 lg:px-8">
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">User Name</th>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Order ID</th>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Address</th>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Total Amount</th>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Payment Method</th>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Date Created</th>
                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                    <span class="sr-only">View Details</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
<?php
// Loop through the results and display them in the table
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0'>" . htmlspecialchars($row['user_name']) . "</td>";
        echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900'>" . htmlspecialchars($row['order_id']) . "</td>";
        echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900'>" . htmlspecialchars($row['shipping_address'] ?? 'N/A') . "</td>";
        echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900'>₱ " . number_format($row['amount'], 2) . "</td>";
        echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900'>" . htmlspecialchars($row['payment_method']) . "</td>";
        echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900'>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900'>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
        echo "<td class='relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0'>";
        echo "<a href='index.php?page=view-order&id=" . $row['id'] . "' class='text-indigo-600 hover:text-indigo-900'>View Details</a>";
        
        // Add Update Status button based on the current status
        if ($row['status'] === 'Order Placed') {
            $nextStatus = 'Preparing';
        } elseif ($row['status'] === 'Preparing') {
            $nextStatus = 'Out For Delivery';
        } else {
            $nextStatus = null; // No further updates possible
        }

        if ($nextStatus) {
            echo " | <button 
                    class='text-blue-600 hover:text-blue-900' 
                    onclick='updateStatus(" . $row['id'] . ", \"$nextStatus\")'>Update to " . ucfirst(str_replace('_', ' ', $nextStatus)) . "</button>";
        }

        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center py-4 text-sm text-gray-500'>No orders found.</td></tr>";
}
?>
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function updateStatus(orderId, newStatus) {

    if (confirm(`Are you sure you want to update the status to ${newStatus.replace('_', ' ')}?`)) {
        fetch('update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ order_id: orderId, new_status: newStatus }),
        })
        .then(response => {
            // Ensure we check if the response is valid
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json(); // Parse the JSON response
        })
        .then(data => {
            if (data.success) {
                alert('Status updated successfully!');
                window.location.reload();
            } else {
                alert('Failed to update status: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status.');
        });
    }
}
</script>



<?php
// Close the database connection
$conn->close();
?>
