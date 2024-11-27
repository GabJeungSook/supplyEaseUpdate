<?php
// Start the session to track login state
session_start();

// Include database connection
include('config.php');

// Fetch categories from the database
$query = "SELECT id, name FROM categories";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get selected category ID or default to showing all products
$selectedCategoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Fetch products based on the selected category or all products
if ($selectedCategoryId) {
    $query = "SELECT * FROM products WHERE category_id = $selectedCategoryId";
} else {
    $query = "SELECT * FROM products";
}

$productsResult = mysqli_query($conn, $query);

if (!$productsResult) {
    die("Database query failed: " . mysqli_error($conn));
}

$products = mysqli_fetch_all($productsResult, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>SupplyEase</title>
</head>
<body>
<div class="bg-white">
  <header class="relative">
    <nav aria-label="Top">
      <div class="bg-green-900">
        <div class="mx-auto flex h-10 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
          <div></div>
          <div class="flex items-center space-x-6">
          <?php if (isset($_SESSION['user_name'])): ?>
              <!-- If user is logged in, show name and logout button -->
              <span class="text-lg font-medium text-white border-r-2 pr-4">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
              <a href="my-orders.php" class="text-sm font-medium text-white hover:text-gray-100">My Profile</a>
              <a href="my-orders.php" class="text-sm font-medium text-white hover:text-gray-100">Contact Admin</a>
              <a href="logout.php" class="text-sm font-medium text-white hover:text-gray-100">Log out</a>
                <!-- Cart Icon -->
            <a href="cart.php" class="text-sm font-medium text-white hover:text-gray-100">
              <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
              </svg>
            </a>
            <?php else: ?>
              <!-- If user is not logged in, show login and create account buttons -->
              <a href="login.php" class="cursor-pointer text-sm font-medium text-white hover:text-gray-100">Log in</a>
              <a href="register.php" class="text-sm font-medium text-white hover:text-gray-100">Create an account</a>
            <?php endif; ?>
          
          </div>
        </div>
      </div>

      <!-- Secondary navigation -->
      <div class="bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 items-center justify-between">
          <a href="#">
                <span class="sr-only">Your Company</span>
                <img class="h-48 w-auto -mt-3" src="resources/supplyEase_logo.png" alt="">
              </a>
            <!-- <div class="hidden lg:flex lg:flex-1 lg:items-center">
             
            </div> -->

            <div class="hidden h-full lg:flex">
              <div class="inset-x-0 bottom-0 px-4">
                <div class="flex h-full justify-center space-x-8">
                  <div class="flex">
                    <div class="relative flex space-x-5">
                      <!-- All category and dynamic categories -->
                      <a href="?category_id=0" class="relative flex items-center justify-center text-sm font-medium text-gray-700 transition-colors duration-200 ease-out hover:text-gray-800">
                        All
                      </a>
                      <?php
                            // Render category buttons
                            foreach ($categories as $category) {
                                $isActive = $category['id'] === $selectedCategoryId ? 'text-green-500' : 'text-gray-700';
                                echo '
                                <a href="?category_id=' . $category['id'] . '" class="relative flex items-center justify-center text-sm font-medium ' . $isActive . ' transition-colors duration-200 ease-out hover:text-gray-800">
                                    '.htmlspecialchars($category['name']).'
                                </a>
                                ';
                            }
                       ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <!-- Products Section -->
    <div class="bg-white">
  <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
    <h2 class="font-semibold mb-4 text-xl">Products</h2>
    <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
    
    
    <?php
        if ($products) {
            foreach ($products as $product) {
                echo '
                 <a href="product_details.php?id=' . $product['id'] . '" class="group">
                    <img src="http://localhost/SupplyEaseUpdate/admin/pages/' . htmlspecialchars($product['image1']) . '" alt="' . htmlspecialchars($product['description']) . '" alt="Tall slender porcelain bottle with natural clay textured body and cork stopper." class="aspect-square w-full rounded-lg bg-gray-200 object-cover group-hover:opacity-75 xl:aspect-[7/8]">
                    <h3 class="mt-4 text-sm text-gray-700">' . htmlspecialchars($product['description']) . '</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">₱ ' . htmlspecialchars($product['price']) . '</p>
                </a>
                ';
            }
        } else {
            echo '<p class="text-gray-500">No products available.</p>';
        }
        ?>
        </div>
      <!-- More products... -->
    </div>
  </div>
</div>
  </main>

</div>
</body>
</html>
