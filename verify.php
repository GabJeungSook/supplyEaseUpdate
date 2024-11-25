<?php
// Start the session to track verification state
session_start();

// Include the database configuration
include('config.php');

// If the form is submitted, check the verification code
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $verification_code = $_POST['verification_code'];
    $email = $_SESSION['email'];  // Store email in session after registration

    // Check the verification code in the database
    $sql = "SELECT * FROM users WHERE email = ? AND verification_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $email, $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Code is correct, update the user status to verified
        $sql_update = "UPDATE users SET is_verified = 1 WHERE email = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param('s', $email);
        $stmt_update->execute();

        $_SESSION['verification_success'] = "Your email has been verified successfully!";
        // Redirect to the login page after verification
        header('Location: login.php');
        exit();
    } else {
        $_SESSION['verification_error'] = "Invalid verification code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Verify Email - SupplyEase</title>
</head>
<body>
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <img class="mx-auto h-[250px] w-[250px]" src="resources/supplyEase_logo.png" alt="Your Company">
    <h2 class="text-center text-2xl/9 font-bold tracking-tight text-gray-900">Verify your account</h2>
  </div>

  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <!-- Show error message if verification failed -->
    <?php if (isset($_SESSION['verification_error'])): ?>
      <div class="text-red-600 text-center mb-4"><?php echo $_SESSION['verification_error']; ?></div>
      <?php unset($_SESSION['verification_error']); ?>
    <?php endif; ?>

    <!-- Verification Form -->
    <form class="space-y-6" action="verify.php" method="POST">
      <div class="mt-5">
        <label for="verification_code" class="block text-sm font-medium text-gray-900">Enter your verification code</label>
        <div class="mt-2">
          <input id="verification_code" name="verification_code" type="number" required class="text-center block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm">
        </div>
      </div>

      <div>
        <button type="submit" class="flex w-full justify-center rounded-md bg-green-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">Submit</button>
      </div>
      <div>
        <a href="index.php" class="flex w-full justify-center rounded-md border border-gray-500 bg-gray-50 px-3 py-1.5 text-sm font-semibold text-gray-800 shadow-sm hover:bg-gray-100">Cancel</a>
      </div>
    </form>

  </div>
</div>

<!-- Display success message if email is verified -->
<?php if (isset($_SESSION['verification_success'])): ?>
<script>
    alert("<?php echo $_SESSION['verification_success']; ?>");
</script>
<?php
// Unset the session variable after showing the alert to prevent it from showing again
unset($_SESSION['verification_success']);
endif;
?>

</body>
</html>
