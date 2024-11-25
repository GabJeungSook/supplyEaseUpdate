<?php
// Include the database connection
require_once '../config.php'; // Assuming config.php contains the database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the category name from the form
    $name = trim($_POST['name']);

    // Check if the name is not empty
    if (!empty($name)) {
        // Insert category into the database
        $sql = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);

        // Execute the query and check if the insert was successful
        if ($stmt->execute()) {
            echo "<script>alert('Category added successfully!'); window.location.href='index.php?page=categories';</script>";
        } else {
            echo "<script>alert('Error adding category: " . $conn->error . "');</script>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "<script>alert('Category name cannot be empty!');</script>";
    }
}

// Close the database connection
$conn->close();
?>

<div>
    <div class="text-2xl font-semibold">
        <span>Create Category</span>
    </div>
    <form id="category-form" method="POST">
        <div class="space-y-12">
            <div class="pb-12">
                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <label for="name" class="block text-sm/6 font-medium text-gray-900" required>Name</label>
                        <div class="mt-2">
                            <input id="name" name="name" type="text" class="px-3 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="index.php?page=categories" class="text-sm/6 font-semibold text-gray-900">Cancel</a>
            <button type="submit" onclick="return confirmCategory()" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
        </div>
    </form>
</div>

<script>
    function confirmCategory() {
        // Show confirmation alert
        return confirm('Are you sure you want to add this category?');
    }
</script>
