<?php
// Start the session to track login state
session_start();

// Include the database configuration and PHPMailer
include('config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is correctly autoloaded

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user'; // Default role, modify if needed
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];

    // Validate that passwords match
    if ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = 'Email is already registered.';
        } else {
            // Hash the password before storing it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Generate a verification code
            $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

            // Insert into the `users` table
            $sql = "INSERT INTO users (name, email, password, role, verification_code) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssss', $name, $email, $hashed_password, $role, $verification_code);

            if ($stmt->execute()) {
                // Get the user_id of the newly inserted user
                $user_id = $stmt->insert_id;

                // Insert into the `user_details` table
                $sql_details = "INSERT INTO user_details (user_id, full_address, gender, contact_number, birthday) VALUES (?, ?, ?, ?, ?)";
                $stmt_details = $conn->prepare($sql_details);
                $stmt_details->bind_param('issss', $user_id, $address, $gender, $contact_number, $birthday);
                $_SESSION['email'] = $email;
                if ($stmt_details->execute()) {
                    // Send verification email
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'julieticawalo07@gmail.com';
                        $mail->Password = 'uivvizeoyrcfvmvk';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port = 465;
                        $mail->setFrom('julieticawalo07@gmail.com', 'SupplyEase');
                        $mail->addAddress($email, $name);
                        $mail->isHTML(true);
                        $mail->Subject = 'Email verification';
                        $mail->Body = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';

                        $mail->send();

                        $_SESSION['register_success'] = 'Registration successful! Please check your email for the verification code.';
                        // Redirect to verification page
                        header('Location: verify.php');
                        exit();
                    } catch (Exception $e) {
                        $error_message = 'There was an error sending the verification email: ' . $mail->ErrorInfo;
                    }
                } else {
                    $error_message = 'There was an error inserting user details. Please try again.';
                }
            } else {
                $error_message = 'There was an error during registration. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Register</title>
</head>
<body>
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <img class="mx-auto h-[250px] w-[250px]" src="resources/supplyEase_logo.png" alt="Your Company">
    <h2 class="text-center text-2xl/9 font-bold tracking-tight text-gray-900">Create an account</h2>
  </div>

  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <!-- Show error message if registration failed -->
    <?php if (isset($error_message)): ?>
      <div class="text-red-600 text-center mb-4"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Registration Form -->
    <form class="space-y-6" action="register.php" method="POST">
      <div>
        <label for="name" class="block text-sm/6 font-medium text-gray-900">Full Name</label>
        <div class="mt-2">
          <input id="name" name="name" type="text" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6">
        </div>
      </div>

      <div>
        <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
        <div class="mt-2">
          <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6">
        </div>
      </div>

      <div>
        <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
        <div class="mt-2">
          <input id="password" name="password" type="password" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6">
        </div>
      </div>

      <div>
        <label for="confirm_password" class="block text-sm/6 font-medium text-gray-900">Confirm Password</label>
        <div class="mt-2">
          <input id="confirm_password" name="confirm_password" type="password" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6">
        </div>
      </div>

      <!-- Add Gender Field -->
      <div>
        <label for="gender" class="block text-sm/6 font-medium text-gray-900">Gender</label>
        <div class="mt-2">
          <select id="gender" name="gender" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6">
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
          </select>
        </div>
      </div>

      <!-- Add Birthday Field -->
      <div>
        <label for="birthday" class="block text-sm/6 font-medium text-gray-900">Birthday</label>
        <div class="mt-2">
          <input id="birthday" name="birthday" type="date" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6">
        </div>
      </div>

      <!-- Add Address Field -->
      <div>
        <label for="address" class="block text-sm/6 font-medium text-gray-900">Address</label>
        <div class="mt-2">
          <textarea id="address" name="address" rows="3" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6"></textarea>
        </div>
      </div>

      <!-- Add Contact Number Field -->
      <div>
        <label for="contact_number" class="block text-sm/6 font-medium text-gray-900">Contact Number</label>
        <div class="mt-2">
          <input id="contact_number" name="contact_number" type="text" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6">
        </div>
      </div>

      <div>
        <button type="submit" class="flex w-full justify-center rounded-md bg-green-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">Register</button>
      </div>
      <div>
        <a href="login.php" class="flex w-full justify-center rounded-md border border-gray-500 bg-gray-50 px-3 py-1.5 text-sm/6 font-semibold text-gray-800 shadow-sm hover:bg-gray-100">Already have an account? Login</a>
      </div>
    </form>
  </div>
</div>
<?php
// Display the success login alert if set
if (isset($_SESSION['register_success'])):
?>
<script>
    alert("<?php echo $_SESSION['register_success']; ?>");
</script>
<?php
// Unset the session variable after showing the alert to prevent it from showing again
unset($_SESSION['register_success']);
endif;
?>
</body>
</html>
