<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .step-indicator {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .step {
            text-align: center;
            flex: 1;
        }

        .step.active {
            color: green;
        }

        .otp-input {
            width: 3rem;
            height: 3rem;
            text-align: center;
            font-size: 1.5rem;
            margin: 0 5px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        .otp-input:focus {
            outline: none;
            border-color: #ffa07a;
            box-shadow: 0 0 5px rgba(255, 160, 122, 0.5);
        }
    </style>
</head>

<body>



<?php include 'navbar.php'; ?>
    <div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center">
        <!-- Step Indicator -->
        <div class="step-indicator mb-4">
            <div class="step active">
                <span>1</span>
                <p class="mb-0">Verify phone no.</p>
            </div>
            <div class="step">
                <span>2</span>
                <p class="mb-0">Create password</p>
            </div>
            <div class="step">
                <span>&#10003;</span>
                <p class="mb-0">Done</p>
            </div>
        </div>
        <div class="password">
            <h1 class="bold" input="type" placeholder="Enter new password..."> Change Password </h1>
            


        </div>

        <!-- Verification Card -->
        <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
            <div class="card-body text-center">
                <button class="btn btn-link text-decoration-none mb-3 text-start" onclick="history.back();">
                    &larr; Back
                </button>
                <h5 class="card-title">Enter Verification Code</h5>
                <p class="text-muted mb-4">Your verification code is sent by Gmail to</p>
                <p class="fw-bold mb-4">kimwency.17@gmail.com</p>

                <!-- OTP Input -->
                <div class="d-flex justify-content-center mb-3">
                    <input type="text" maxlength="1" class="otp-input">
                    <input type="text" maxlength="1" class="otp-input">
                    <input type="text" maxlength="1" class="otp-input">
                    <input type="text" maxlength="1" class="otp-input">
                    <input type="text" maxlength="1" class="otp-input">
                    <input type="text" maxlength="1" class="otp-input">
                </div>

                <p class="text-muted mb-4">Please wait <span class="fw-bold">57 seconds</span> to resend.</p>

                <!-- Next Button -->
                <button class="btn btn-outline-warning bg-warning text-black btn-block" type="button">NEXT</button>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus on the next input
        document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
            input.addEventListener('input', () => {
                if (input.value && inputs[index + 1]) {
                    inputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && inputs[index - 1]) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
</body>

</html>
