<?php
// Include database connection
include('config.php');

// Get product ID from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product details from the database
$query = "
    SELECT 
        products.*, 
        categories.name AS category_name 
    FROM 
        products 
    JOIN 
        categories 
    ON 
        products.category_id = categories.id 
    WHERE 
        products.id = $productId
";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

$product = mysqli_fetch_assoc($result);

// Check if the product exists
if (!$product) {
    die("Product not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title><?php echo htmlspecialchars($product['description']); ?></title>
</head>
<body>
<div class="bg-white">
    <div class="pl-40 p-5 text-2xl font-semibold">
        <span>Product Details</span>
    </div>
  <div class="pb-16 pt-6 sm:pb-24">
    <div class="mx-auto mt-8 max-w-2xl px-4 sm:px-6 lg:max-w-7xl lg:px-8">
      <div class="lg:grid lg:auto-rows-min lg:grid-cols-12 lg:gap-x-8">
        <div class="lg:col-span-5 lg:col-start-8">
          <div class="flex justify-between">
            <h1 class="text-xl font-medium text-gray-900"><?php echo htmlspecialchars($product['description']); ?></h1>
            <p class="text-xl font-medium text-gray-900">₱<?php echo number_format($product['price'], 2); ?></p>
          </div>
          
          <!-- Reviews -->
          <div class="mt-4">
            <h2 class="sr-only">Reviews</h2>
            <div class="flex items-center">
              <div aria-hidden="true" class="ml-4 text-sm text-gray-300">·</div>
            </div>
          </div>
        </div>

        <!-- Image gallery (Carousel) -->
        <div class="mt-8 lg:col-span-7 lg:col-start-1 lg:row-span-3 lg:row-start-1 lg:mt-0">
          <h2 class="sr-only">Images</h2>

          <div class="relative">
            <!-- Image Container -->
            <div class="carousel-container relative">
              <!-- Main product image -->
              <img src="http://146.190.85.108/admin/pages/<?php echo htmlspecialchars($product['image1']); ?>" 
                   alt="Main image of <?php echo htmlspecialchars($product['description']); ?>" 
                   class="carousel-item rounded-lg w-full h-[400px] object-cover mb-4">
              <?php if (!empty($product['image2'])): ?>
              <img src="http://146.190.85.108/admin/pages/<?php echo htmlspecialchars($product['image2']); ?>" 
                   alt="Secondary image of <?php echo htmlspecialchars($product['description']); ?>" 
                   class="carousel-item hidden rounded-lg w-full h-[400px] object-cover">
              <?php endif; ?>
              <?php if (!empty($product['image3'])): ?>
              <img src="http://146.190.85.108/admin/pages/<?php echo htmlspecialchars($product['image3']); ?>" 
                   alt="Third image of <?php echo htmlspecialchars($product['description']); ?>" 
                   class="carousel-item hidden rounded-lg w-full h-[400px] object-cover">
              <?php endif; ?>
            </div>

            <!-- Carousel Controls (Next and Previous) -->
            <button id="prevBtn" class="absolute top-1/2 left-2 transform -translate-y-1/2 text-white bg-black bg-opacity-50 p-2 rounded-full">
              &#8249;
            </button>
            <button id="nextBtn" class="absolute top-1/2 right-2 transform -translate-y-1/2 text-white bg-black bg-opacity-50 p-2 rounded-full">
              &#8250;
            </button>
          </div>
        </div>

        <div class="mt-8 lg:col-span-5">
          <form method="POST" action="add_to_cart.php">
            <!-- Quantity input -->
            <div class="mb-4">
              <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
              <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>"
       class="p-4 mt-1 block w-full rounded-md border border-gray-500 shadow-sm sm:text-sm">

            </div>

            <!-- Add to cart button -->
            <button type="submit" onclick="return confirmAddToCart()" name="product_id" value="<?php echo $product['id']; ?>" class="mt-8 flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
              Add to cart
            </button>

            <a href="index.php" class="mt-2 flex w-full items-center justify-center rounded-md border border-gray-500 bg-gray-50 px-8 py-3 text-base font-medium text-gray-800 hover:bg-gray-200">
              Cancel
            </a>
          </form>

          <!-- Product details -->
          <div class="mt-10">
            <h2 class="text-sm font-medium text-gray-900">Category</h2>
            <div class="space-y-4 text-sm text-gray-500">
              <p><?php echo $product['category_name']?></p>
            </div>
            <h2 class="mt-4 text-sm font-medium text-gray-900">Available Stock</h2>
            <div class="space-y-4 text-sm text-gray-500">
              <p><?php echo $product['stock']?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Carousel functionality
let currentIndex = 0;
const items = document.querySelectorAll('.carousel-item');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

function showItem(index) {
  // Hide all items
  items.forEach(item => item.classList.add('hidden'));
  // Show the current item
  items[index].classList.remove('hidden');
}

// Show the first item initially
showItem(currentIndex);

// Next button click event
nextBtn.addEventListener('click', () => {
  currentIndex = (currentIndex + 1) % items.length;
  showItem(currentIndex);
});

// Previous button click event
prevBtn.addEventListener('click', () => {
  currentIndex = (currentIndex - 1 + items.length) % items.length;
  showItem(currentIndex);
});

function confirmAddToCart() {
        // Show confirmation alert
        return confirm('Are you sure you want to add this product to your cart?');
}
</script>

</body>
</html>
