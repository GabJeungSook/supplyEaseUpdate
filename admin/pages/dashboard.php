<?php
// Include the database connection
include('../config.php');

// Fetch total categories
$totalCategoriesQuery = "SELECT COUNT(*) AS total_categories FROM categories";
$resultCategories = $conn->query($totalCategoriesQuery);
$totalCategories = $resultCategories->fetch_assoc()['total_categories'];

// Fetch total products
$totalProductsQuery = "SELECT COUNT(*) AS total_products FROM products";
$resultProducts = $conn->query($totalProductsQuery);
$totalProducts = $resultProducts->fetch_assoc()['total_products'];

// Fetch total orders
$totalOrdersQuery = "SELECT COUNT(*) AS total_orders FROM payments where status != 'Cancelled'";
$resultOrders = $conn->query($totalOrdersQuery);
$totalOrders = $resultOrders->fetch_assoc()['total_orders'];

// Fetch total sales
$totalSalesQuery = "SELECT SUM(amount) AS total_sales FROM payments WHERE status = 'Completed'";
$resultSales = $conn->query($totalSalesQuery);
$totalSales = $resultSales->fetch_assoc()['total_sales'];

?>

<div>
    <div class="text-2xl font-semibold">
        <span>Dashboard</span>
    </div>
    <!-- stats -->
    <div class="mt-8 p-4 bg-gray-800">
        <dl class="mx-auto grid grid-cols-1 gap-px bg-gray-800 sm:grid-cols-2 lg:grid-cols-4">
            <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                <dt class="text-sm/6 font-medium text-gray-500">Total Categories</dt>
                <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900"><?php echo number_format($totalCategories); ?></dd>
            </div>
            <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                <dt class="text-sm/6 font-medium text-gray-500">Total Products</dt>
                <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900"><?php echo number_format($totalProducts); ?></dd>
            </div>
            <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                <dt class="text-sm/6 font-medium text-gray-500">Total Orders</dt>
                <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900"><?php echo number_format($totalOrders); ?></dd>
            </div>
            <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                <dt class="text-sm/6 font-medium text-gray-500">Total Sales</dt>
                <dd class="w-full flex-none text-3xl/10 font-medium tracking-tight text-gray-900"><?php echo '₱ ' . number_format($totalSales, 2); ?></dd>
            </div>
        </dl>
    </div>
</div>

<?php
// Close the database connection
$conn->close();
?>
