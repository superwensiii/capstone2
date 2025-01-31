<?php
// Start the session
session_start();

// Check if the email is verified
if (!isset($_SESSION['email_verified']) || !$_SESSION['email_verified']) {
    header("Location: otp_request.php"); // Redirect back to OTP request page
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the registration form data
    $fullName = $_POST['full_name'];
    $password = $_POST['password'];

    // Save the user data (e.g., to a database)
    // For now, just display a success message
    echo "Registration successful! Welcome, $fullName.";

    // Clear the session
    session_unset();
    session_destroy();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
</head>
<body>
    <h1>Registration Form</h1>
    <form method="POST" action="">
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" id="full_name" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>