<?php
// Include the database connection
require_once '../config.php'; // Assuming config.php contains the database connection

// Fetch orders from the payments table along with the user's name
$sql = "SELECT p.id, p.order_id, p.amount, p.created_at, u.name AS user_name 
        FROM payments p
        JOIN users u ON p.user_id = u.id"; // Joining with the users table to get the user's name
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
    <div class="mt-10 px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <!-- <h1 class="text-base font-semibold text-gray-900">Orders</h1> -->
                <!-- <p class="mt-2 text-sm text-gray-700">A list of all the orders in your system.</p> -->
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <a href="index.php?page=create-order" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add Order</a>
            </div>
        </div>
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">User Name</th>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Order ID</th>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Total Amount</th>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Date Created</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
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
                                    echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900'>₱ " . number_format($row['amount'], 2) . "</td>";
                                    echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900'>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
                                    echo "<td class='relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0'>";
                                    
                                    // Update the edit link to pass the order id
                                    echo "<a href='index.php?page=view-order&id=" . $row['id'] . "' class='text-indigo-600 hover:text-indigo-900'>View Details<span class='sr-only'>, Order " . htmlspecialchars($row['order_id']) . "</span></a>";
                                    
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-4 text-sm text-gray-500'>No orders found.</td></tr>";
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Close the database connection
$conn->close();
?>
