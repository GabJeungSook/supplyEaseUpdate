<?php
// Include the database connection
require_once '../config.php';

// Fetch categories from the database for the dropdown
$sql = "SELECT id, name FROM categories";
$result = $conn->query($sql);

// Check for errors
if ($result === false) {
    die("Error fetching categories: " . $conn->error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the product data from the form
    $category_id = $_POST['category_id'];
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Initialize image paths as null
    $image1 = $image2 = $image3 = null;

    // Set the relative target directory for images
    $target_dir = 'images/';

    // Ensure the target directory exists
    if (!is_dir(__DIR__ . '/' . $target_dir)) {
        mkdir(__DIR__ . '/' . $target_dir, 0777, true); // Create the directory with write permissions
    }

    // Handle image1 upload
    if (isset($_FILES['image1']) && $_FILES['image1']['error'] === UPLOAD_ERR_OK) {
        $filename1 = uniqid() . '_' . basename($_FILES['image1']['name']); // Add a unique prefix
        $image1 = $target_dir . $filename1; // Save the relative path
        move_uploaded_file($_FILES['image1']['tmp_name'], __DIR__ . '/' . $image1);
    }

    // Handle image2 upload
    if (isset($_FILES['image2']) && $_FILES['image2']['error'] === UPLOAD_ERR_OK) {
        $filename2 = uniqid() . '_' . basename($_FILES['image2']['name']); // Add a unique prefix
        $image2 = $target_dir . $filename2; // Save the relative path
        move_uploaded_file($_FILES['image2']['tmp_name'], __DIR__ . '/' . $image2);
    }

    // Handle image3 upload
    if (isset($_FILES['image3']) && $_FILES['image3']['error'] === UPLOAD_ERR_OK) {
        $filename3 = uniqid() . '_' . basename($_FILES['image3']['name']); // Add a unique prefix
        $image3 = $target_dir . $filename3; // Save the relative path
        move_uploaded_file($_FILES['image3']['tmp_name'], __DIR__ . '/' . $image3);
    }

    // Prepare the SQL query to insert the product
    $sql = "INSERT INTO products (category_id, description, price, stock, image1, image2, image3) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdisss", $category_id, $description, $price, $stock, $image1, $image2, $image3);

    // Execute the query and check if the insert was successful
    if ($stmt->execute()) {
        echo "<script>alert('Product added successfully!'); window.location.href='index.php?page=products';</script>";
    } else {
        echo "<script>alert('Error adding product: " . $conn->error . "');</script>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>


<div>
    <div class="text-2xl font-semibold">
        <span>Create Product</span>
    </div>
    <form id="product-form" method="POST" enctype="multipart/form-data">
        <div class="space-y-12">
            <div class="pb-12">
                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <!-- Category Selection -->
                    <div class="sm:col-span-4">
                        <label for="category_id" class="block text-sm/6 font-medium text-gray-900">Category</label>
                        <div class="mt-2">
                            <select id="category_id" name="category_id" class="px-3 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:max-w-xs sm:text-sm/6">
                                <option value="">Select a category</option>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Product Description -->
                    <div class="col-span-full">
                        <label for="description" class="block text-sm/6 font-medium text-gray-900">Description</label>
                        <div class="mt-2">
                            <input id="description" name="description" type="text" class="px-3 block w-1/2 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6" required>
                        </div>
                    </div>

                    <!-- Product Price -->
                    <div class="col-span-full">
                        <label for="price" class="block text-sm/6 font-medium text-gray-900">Price</label>
                        <div class="mt-2">
                            <input id="price" name="price" type="number" step="0.01"  class="px-3 block w-1/2 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6" required>
                        </div>
                    </div>

                    <!-- Product Stock -->
                    <div class="col-span-full">
                        <label for="stock" class="block text-sm/6 font-medium text-gray-900">Stock</label>
                        <div class="mt-2">
                            <input id="stock" name="stock" type="number" step="0.01"  class="px-3 block w-1/2 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6" required>
                        </div>
                    </div>


                    <!-- Product Images -->
                    <div class="sm:col-span-6">
                        <label for="image1" class="block text-sm/6 font-medium text-gray-900">Image 1</label>
                        <input id="image1" name="image1" type="file" class="block w-full text-sm text-gray-900" />
                    </div>
                    <div class="sm:col-span-6">
                        <label for="image2" class="block text-sm/6 font-medium text-gray-900">Image 2</label>
                        <input id="image2" name="image2" type="file" class="block w-full text-sm text-gray-900" />
                    </div>
                    <div class="sm:col-span-6">
                        <label for="image3" class="block text-sm/6 font-medium text-gray-900">Image 3</label>
                        <input id="image3" name="image3" type="file" class="block w-full text-sm text-gray-900" />
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="index.php?page=products" class="text-sm/6 font-semibold text-gray-900">Cancel</a>
            <button type="submit" onclick="return confirmProduct()" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
        </div>
    </form>
</div>

<script>
    function confirmProduct() {
        // Show confirmation alert
        return confirm('Are you sure you want to add this product?');
    }
</script>
