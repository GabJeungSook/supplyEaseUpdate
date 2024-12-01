<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$success_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save the concern to the database
    $concern = trim($_POST['concern']);

    if (!empty($concern)) {
        $insert_query = "INSERT INTO customer_concerns (user_id, concern, created_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param('is', $user_id, $concern);
        if ($stmt->execute()) {
            $success_message = "Your concern has been submitted successfully.";
        } else {
            $error_message = "Failed to submit your concern. Please try again.";
        }
    } else {
        $error_message = "The concern field cannot be empty.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>SupplyEase - Contact Admin</title>
</head>
<body class="bg-gray-200">
    <a href="index.php" class="p-3 inline-block mb-6 bg-green-900 rounded-lg m-3 text-gray-50 hover:text-gray-100">
        &larr; Back to Home
    </a>
    <div class="max-w-2xl mx-auto bg-gray-100 p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6">Contact Admin</h1>
        <?php if (isset($error_message)): ?>
            <div class="mb-4 text-red-600 bg-red-100 p-3 rounded">
                <?= htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-4">
                <label for="concern" class="block text-gray-900 font-medium mb-2">Your Message</label>
                <textarea 
                    id="concern" 
                    name="concern" 
                    rows="5" 
                    class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500" 
                    placeholder="Write your message here..."></textarea>
            </div>
            <button 
                type="submit" 
                class="w-full bg-green-800 text-white p-3 rounded-lg hover:bg-green-700 focus:outline-none">
                Submit
            </button>
        </form>
    </div>

    <?php if (!is_null($success_message)): ?>
        <script>
            alert("<?= addslashes($success_message); ?>");
            window.location.href = "index.php"; // Redirect after showing the alert
        </script>
    <?php endif; ?>
</body>
</html>
