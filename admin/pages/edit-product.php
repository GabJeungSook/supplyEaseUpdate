<?php
// Include the database connection
require_once '../config.php';

// Fetch categories for the dropdown
$sql_categories = "SELECT id, name FROM categories";
$result_categories = $conn->query($sql_categories);

// Check for errors in fetching categories
if ($result_categories === false) {
    die("Error fetching categories: " . $conn->error);
}

// Fetch the product to edit
$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    die("Product ID is required.");
}

$sql_product = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql_product);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) {
    die("Product not found.");
}
$stmt->close();

// Update the product if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get updated product data
    $category_id = $_POST['category_id'];
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Handle image uploads
    $image1 = $product['image1'];
    $image2 = $product['image2'];
    $image3 = $product['image3'];

    // Define the relative path to the images folder
    $target_dir = 'images/';

    // Ensure the images folder exists
    $absolute_dir = __DIR__ . '/' . $target_dir;
    if (!is_dir($absolute_dir)) {
        mkdir($absolute_dir, 0777, true);
    }

    // Handle image1 upload
    if (isset($_FILES['image1']) && $_FILES['image1']['error'] === UPLOAD_ERR_OK) {
        $image1 = $target_dir . basename($_FILES['image1']['name']);
        move_uploaded_file($_FILES['image1']['tmp_name'], $absolute_dir . basename($_FILES['image1']['name']));
    }

    // Handle image2 upload
    if (isset($_FILES['image2']) && $_FILES['image2']['error'] === UPLOAD_ERR_OK) {
        $image2 = $target_dir . basename($_FILES['image2']['name']);
        move_uploaded_file($_FILES['image2']['tmp_name'], $absolute_dir . basename($_FILES['image2']['name']));
    }

    // Handle image3 upload
    if (isset($_FILES['image3']) && $_FILES['image3']['error'] === UPLOAD_ERR_OK) {
        $image3 = $target_dir . basename($_FILES['image3']['name']);
        move_uploaded_file($_FILES['image3']['tmp_name'], $absolute_dir . basename($_FILES['image3']['name']));
    }

    // Update product data in the database
    $sql_update = "UPDATE products SET category_id = ?, description = ?, price = ?, stock = ?, image1 = ?, image2 = ?, image3 = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("isdisssi", $category_id, $description, $price, $stock, $image1, $image2, $image3, $product_id);

    if ($stmt_update->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location.href='index.php?page=products';</script>";
    } else {
        echo "<script>alert('Error updating product: " . $conn->error . "');</script>";
    }

    $stmt_update->close();
}

$conn->close();
?>


<div>
    <div class="text-2xl font-semibold">
        <span>Edit Product</span>
    </div>
    <form id="edit-product-form" method="POST" enctype="multipart/form-data">
        <div class="space-y-12">
            <div class="pb-12">
                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    <!-- Category Selection -->
                    <div class="sm:col-span-4">
                        <label for="category_id" class="block text-sm/6 font-medium text-gray-900">Category</label>
                        <div class="mt-2">
                            <select id="category_id" name="category_id" class="px-3 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:max-w-xs sm:text-sm/6">
                                <option value="">Select a category</option>
                                <?php while ($row = $result_categories->fetch_assoc()) { ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Product Description -->
                    <div class="col-span-full">
                        <label for="description" class="block text-sm/6 font-medium text-gray-900">Description</label>
                        <div class="mt-2">
                            <input id="description" name="description" type="text" value="<?php echo htmlspecialchars($product['description']); ?>" class="px-3 block w-1/2 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm/6" required>
                        </div>
                    </div>

                    <!-- Product Price -->
                    <div class="col-span-full">
                        <label for="price" class="block text-sm/6 font-medium text-gray-900">Price</label>
                        <div class="mt-2">
                            <input id="price" name="price" type="number" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" class="px-3 block w-1/2 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm/6" required>
                        </div>
                    </div>

                    <!-- Product Stock -->
                    <div class="col-span-full">
                        <label for="stock" class="block text-sm/6 font-medium text-gray-900">Stock</label>
                        <div class="mt-2">
                            <input id="stock" name="stock" type="number" value="<?php echo htmlspecialchars($product['stock']); ?>" class="px-3 block w-1/2 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm/6" required>
                        </div>
                    </div>

                    <!-- Product Images -->
                    <div class="sm:col-span-6">
                        <label for="image1" class="block text-sm/6 font-medium text-gray-900">Image 1</label>
                        <input id="image1" name="image1" type="file" class="block w-full text-sm text-gray-900" />
                        <?php 
                        if ($product['image1']) { 
                            // Construct the correct path for the image
                            $imagePath = str_replace('C:\\xampp\\htdocs\\SupplyEaseUpdate\\admin\\pages\\', '', $product['image1']);
                            echo "<img src='http://146.190.85.108/SupplyEaseUpdate/admin/pages/" . htmlspecialchars($imagePath) . "' class='mt-2' alt='Image 2' width='200' />";
                        } 
                        ?>
                    </div>
                    <div class="sm:col-span-6">
                        <label for="image2" class="block text-sm/6 font-medium text-gray-900">Image 2</label>
                        <input id="image2" name="image2" type="file" class="block w-full text-sm text-gray-900" />
                        <?php 
                        if ($product['image2']) { 
                            // Construct the correct path for the image
                            $imagePath = str_replace('C:\\xampp\\htdocs\\SupplyEaseUpdate\\admin\\pages\\', '', $product['image2']);
                            echo "<img src='http://146.190.85.108/SupplyEaseUpdate/admin/pages/" . htmlspecialchars($imagePath) . "' class='mt-2' alt='Image 2' width='200' />";
                        } 
                        ?>
                    </div>
                    <!-- <img src="http://localhost/SupplyEaseUpdate/admin/pages/images/shutterstock_1113179420.jpg" alt=""> -->
                    <div class="sm:col-span-6">
                        <label for="image3" class="block text-sm/6 font-medium text-gray-900">Image 3</label>
                        <input id="image3" name="image3" type="file" class="block w-full text-sm text-gray-900" />
                        <?php 
                        if ($product['image3']) { 
                            // Construct the correct path for the image
                            $imagePath = str_replace('C:\\xampp\\htdocs\\SupplyEaseUpdate\\admin\\pages\\', '', $product['image3']);
                            echo "<img src='http://146.190.85.108/SupplyEaseUpdate/admin/pages/" . htmlspecialchars($imagePath) . "' class='mt-2' alt='Image 2' width='200' />";
                        } 
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="index.php?page=products" class="text-sm/6 font-semibold text-gray-900">Cancel</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
        </div>
    </form>
</div>
