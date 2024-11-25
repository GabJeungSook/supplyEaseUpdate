<?php
// Include the database connection
require_once '../config.php'; // Assuming config.php contains the database connection

// Fetch categories from the database
$sql = "SELECT id, name as 'description' FROM categories"; // Modify this query if your table name or columns are different
$result = $conn->query($sql);

// Check for errors
if ($result === false) {
    die("Error fetching categories: " . $conn->error);
}
?>

<div>
    <div class="text-2xl font-semibold">
        <span>Categories</span>
    </div>
    <div class="mt-10 px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <!-- <h1 class="text-base font-semibold text-gray-900">Categories</h1> -->
                <!-- <p class="mt-2 text-sm text-gray-700">A list of all the categories in your system.</p> -->
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <a href="index.php?page=create-category" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add Category</a>
            </div>
        </div>
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Name</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        <?php
                            // Loop through the results and display them in the table
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0'>" . htmlspecialchars($row['description']) . "</td>";
                                    echo "<td class='relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0'>";
                                    
                                    // Update the edit link to pass the category id
                                    echo "<a href='index.php?page=edit-category&id=" . $row['id'] . "' class='text-indigo-600 hover:text-indigo-900'>Edit<span class='sr-only'>, " . htmlspecialchars($row['description']) . "</span></a>";
                                    
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2' class='text-center py-4 text-sm text-gray-500'>No categories found.</td></tr>";
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
