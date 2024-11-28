<?php
// Include the database connection
require_once '../config.php'; // Assuming config.php contains the database connection

// Fetch orders from the payments table along with the user's name
$sql = "SELECT c.id, c.concern, c.created_at, u.name AS user_name 
        FROM customer_concerns c
        JOIN users u ON c.user_id = u.id"; // Joining with the users table to get the user's name
$result = $conn->query($sql);

// Check for errors
if ($result === false) {
    die("Error fetching orders: " . $conn->error);
}
?>

<div>
    <div class="text-2xl font-semibold">
        <span>Concerns</span>
    </div>
    <div class="mt-10 px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <!-- <h1 class="text-base font-semibold text-gray-900">Orders</h1> -->
                <!-- <p class="mt-2 text-sm text-gray-700">A list of all the orders in your system.</p> -->
            </div>
        </div>
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">User Name</th>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Concern</th>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        <?php
                            // Loop through the results and display them in the table
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0'>" . htmlspecialchars($row['user_name']) . "</td>";
                                    echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900'>" . htmlspecialchars($row['concern']) . "</td>";
                                    echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900'>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
                                    echo "<td class='relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0'>";
                                    
                                    // Update the edit link to pass the order id
                                    // echo "<a href='index.php?page=view-order&id=" . $row['id'] . "' class='text-indigo-600 hover:text-indigo-900'>View Details<span class='sr-only'>, Order " . htmlspecialchars($row['id']) . "</span></a>";
                                    
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-4 text-sm text-gray-500'>No record found.</td></tr>";
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
