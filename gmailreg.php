<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login UI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: white;
        }
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        

        .or-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
        }
        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ddd;
        }
        .or-divider:not(:empty)::before {
            margin-right: .5em;
        }
        .or-divider:not(:empty)::after {
            margin-left: .5em;
        }
    </style>
</head>
<body>
    <div class="login-container position-relative">
        <h3 class="text-center mb-4">Log In</h3>
        <form>
            <div class="mb-3">
                <label for="email" class="form-label">Enter your Email</label>
                <input type="text" class="form-control" id="phoneNumber" placeholder="Enter your Email">
            </div>
            <button type="submit" class="btn btn-warning w-100 mb-2">Next</button>
            <a href="main/user_login.php" class="d-block text-center text-primary mb-3">Log in with password</a>
            <div class="or-divider">OR</div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-primary w-50 me-2">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook" width="20" class="me-2">
                    Facebook
                </button>
                <button type="button" class="btn btn-outline-danger w-50 ms-2">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/0/0b/Google_Logo.svg" alt="Google" width="20" class="me-2">
                    Google
                </button>
            </div>
        </form>
        <p class="text-center mt-4">By signing up, you agre to Great Wall Art's <a href="#" class="text-danger">Terms of Service & Privacy Policy</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>e
</html>
