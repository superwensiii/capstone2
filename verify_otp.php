<?php
// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $userOTP = $_POST['otp'];

    // Retrieve the OTP and email from the session
    if (isset($_SESSION['otp']) && isset($_SESSION['otp_email'])) {
        $storedOTP = $_SESSION['otp'];
        $email = $_SESSION['otp_email'];

        // Verify the OTP
        if ($userOTP == $storedOTP) {
            echo "OTP verified successfully! Welcome, $email.";
            // Clear the OTP from the session
            unset($_SESSION['otp']);
            unset($_SESSION['otp_email']);
        } else {
            echo "Invalid OTP. Please try again.";
        }
    } else {
        echo "OTP expired or not found. Please request a new OTP.";
    }
}
?>