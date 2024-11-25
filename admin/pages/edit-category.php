<?php
// Include the database connection
require_once '../config.php'; // Assuming config.php contains the database connection

// Check if 'id' is provided in the URL and fetch the category data
if (isset($_GET['id'])) {
    $categoryId = $_GET['id'];

    // Fetch the category from the database
    $sql = "SELECT * FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
    } else {
        echo "<script>alert('Category not found!'); window.location.href='index.php?page=categories';</script>";
        exit();
    }

    $stmt->close();
}

// Handle form submission for updating the category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the new category name
    $name = trim($_POST['name']);

    // Check if the name is not empty
    if (!empty($name)) {
        // Proceed with the update
        $sql = "UPDATE categories SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $name, $categoryId);

        // Execute the update query
        if ($stmt->execute()) {
            echo "<script>alert('Category updated successfully!'); window.location.href='index.php?page=categories';</script>";
        } else {
            echo "<script>alert('Error updating category: " . $conn->error . "');</script>";
        }

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
        <span>Edit Category</span>
    </div>
    <form id="category-form" method="POST">
        <div class="space-y-12">
            <div class="pb-12">
                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <label for="name" class="block text-sm/6 font-medium text-gray-900" required>Name</label>
                        <div class="mt-2">
                            <input id="name" name="name" type="text" class="px-3 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6" required value="<?php echo htmlspecialchars($category['name']); ?>">
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
