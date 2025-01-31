<?php
session_start();

// Check if the OTP and email are set in the session
if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_email'])) {
    header("Location: otp_request.php"); 
    exit();
}

// Check if the OTP has expired
if (isset($_SESSION['otp_expiry']) && time() > $_SESSION['otp_expiry']) {
    echo "<script>alert('OTP has expired. Please request a new one.');</script>";
    unset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['otp_expiry']);
    header("Location: otp_request.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $userOTP = $_POST['otp'];

    if ($userOTP == $_SESSION['otp']) {
        $_SESSION['email_verified'] = true;
        header("Location: register.php");
        exit();
    } else {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
        }
        h1 {
            color: #333;
        }
        .otp-input {
            padding: 10px;
            font-size: 18px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 10px 0;
        }
        button {
            background-color:  #ffc107;
            ;
            color: black;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 8px;
            font-size: 16px;
        }
    
        }
        .timer {
            color: #d9534f;
            font-size: 14px;
        }
        .back-link {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        let timer = 50;
        const countdownElement = () => {
            const timerDisplay = document.getElementById('timer-display');
            if (timer > 0) {
                timer--;
                timerDisplay.textContent = `Please wait ${timer} seconds to input the OTP.`;
            } else {
                alert("Time's up! Please request a new OTP.");
                window.location.href = "otp_request.php";
            }
        };
        setInterval(countdownElement, 1000);
    </script>
</head>
<body>
    <div class="container">
        <h1>Enter Verification Code</h1>
        <p>Your verification code is sent by Gmail to <p class="text-bold text-warning"><?php echo $_SESSION['otp_email']; ?></p></p>
        <form method="POST">
            <input type="text" name="otp" class="otp-input" placeholder="Enter OTP" required maxlength="6">
            <div id="timer-display" class="timer"></div>
            <button type="submit">Verify OTP</button>
        </form>
        <a href="otp_request.php" class="back-link">&larr; Back</a>
    </div>
</body>
</html>
