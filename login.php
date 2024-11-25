<?php
// Start the session to track login state
session_start();

// Include the database configuration
include('config.php');

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if user exists and retrieve their verification status
    $sql = "SELECT id, name, email, password, role, is_verified FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if user exists, password is correct, and the user is verified
    if ($user && password_verify($password, $user['password'])) {
        // Check if the user is verified
        if ($user['role'] === 'user' && $user['is_verified'] == 0) {
            // If user is not verified, deny login and show an error
            $error_message = 'Your account is not verified. Please check your email for the verification code.';
        } else {
            // Set session variables for user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Set a session variable to show the success alert
            $_SESSION['login_success'] = 'You have successfully logged in!';

            // Redirect to the admin page if the user is an admin
            if ($_SESSION['role'] === 'admin') {
                header('Location: admin/index.php?page=dashboard');
                exit();
            } else {
                // Redirect to the homepage for non-admin users
                header('Location: index.php');
                exit();
            }
        }
    } else {
        // Invalid login credentials
        $error_message = 'Invalid email or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login</title>
</head>
<body>
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img class="mx-auto h-[250px] w-[250px]" src="resources/supplyEase_logo.png" alt="Your Company">
        <h2 class="text-center text-2xl/9 font-bold tracking-tight text-gray-900">Login</h2>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <!-- Show error message if login failed -->
        <?php if (isset($error_message)): ?>
        <div class="text-red-600 text-center mb-4"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Login Form -->
        <form class="space-y-6" action="login.php" method="POST">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-900">Email address</label>
                <div class="mt-2">
                    <input id="email" name="email" type="email" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-900">Password</label>
                <div class="mt-2">
                    <input id="password" name="password" type="password" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm">
                </div>
            </div>

            <div>
                <button type="submit" class="flex w-full justify-center rounded-md bg-green-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">Log in</button>
            </div>
            <div>
                <a href="register.php" class="flex w-full justify-center rounded-md border border-gray-500 bg-gray-50 px-3 py-1.5 text-sm font-semibold text-gray-800 shadow-sm hover:bg-gray-100">Don't have an account? Register</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
