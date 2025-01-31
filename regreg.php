<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Start the session
session_start();

// Function to generate a random OTP
function generateOTP($length = 6) {
    return rand(pow(10, $length - 1), pow(10, $length) - 1);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Generate a random OTP
    $otp = generateOTP();

    // Store the OTP in the session for verification
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $email;

    // Create an instance of PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Disable debug output
        $mail->isSMTP(); // Send using SMTP
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'greatwallartcore@gmail.com'; // SMTP username
        $mail->Password = 'sxwt pmaw zezm ndaj'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable TLS encryption
        $mail->Port = 465; // TCP port to connect to

        // Recipients
        $mail->setFrom('greatwallartcore@gmail.com', 'Great Wall Art');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Your OTP for Login';
        $mail->Body = "Your OTP is: <b>$otp</b>";
        $mail->AltBody = "Your OTP is: $otp";

        // Send the email
        $mail->send();
        echo "OTP has been sent to your email.";
    } catch (Exception $e) {
        echo "Failed to send OTP. Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Login</title>
</head>
<body>
    <h1>OTP Login</h1>
    <form method="POST" action="">
        <label for="email">Enter your email:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Send OTP</button>
    </form>

    <hr>

    <h2>Verify OTP</h2>
    <form method="POST" action="verify_otp.php">
        <label for="otp">Enter OTP:</label>
        <input type="text" name="otp" id="otp" required>
        <button type="submit">Verify OTP</button>
    </form>
</body>
</html>