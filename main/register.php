<?php
// Include database connection
include '../db/connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if all required fields are set
    if (
        isset($_POST['email'], $_POST['first_name'], $_POST['surname'], $_POST['phone'], $_POST['address'], $_POST['password'], $_POST['gender'])
    ) {
        // Get form data and sanitize input
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $surname = mysqli_real_escape_string($conn, $_POST['surname']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing password
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);

        // Validate gender
        $allowed_genders = ['Male', 'Female', 'Other'];
        if (!in_array($gender, $allowed_genders)) {
            echo "<div class='alert alert-danger'>Invalid gender selected!</div>";
            exit;
        }

        // Prepare the SQL query
        $sql = "INSERT INTO users (email, first_name, surname, phone, address, password, gender) 
                VALUES ('$email', '$first_name', '$surname', '$phone', '$address', '$password', '$gender')";

        // Check if the query was successful
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>New record created successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Please fill in all required fields.</div>";
    }
}

// Close the connection
$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f1f3f5;
      margin: 0;
      padding: 0;
    }

    .container-fluid {
      display: flex;
      min-height: 100vh;
      align-items: center;
      justify-content: center;
      background-color: #f9fafb;
    }

    .left-section {
      background: linear-gradient(135deg, #4a90e2, #007bff);
      color: #fff;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 50px;
      text-align: center;
      border-radius: 10px 0 0 10px;
    }

    .left-section h1 {
      font-size: 36px;
      font-weight: bold;
    }

    .right-section {
      flex: 2;
      background-color: #fff;
      padding: 50px;
      border-radius: 0 10px 10px 0;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .form-label {
      font-weight: bold;
    }

    .btn-primary {
      background-color: #4a90e2;
      border: none;
      transition: background-color 0.3s;
    }

    .btn-primary:hover {
      background-color: #007bff;
    }

    .btn-secondary {
      background-color: #f1f3f5;
      border: 1px solid #ccc;
      color: #333;
      transition: background-color 0.3s;
    }

    .btn-secondary:hover {
      background-color: #ddd;
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row w-100">
    <!-- Left Section -->
    

    <!-- Right Section -->
    <div class="col-md-8 right-section">
      <h3 class="mb-4">Create an Account</h3>
      <form action="register.php" method="POST">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
          </div>
          <div class="col-md-6">
            <label for="surname" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="surname" name="surname" required>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
        </div>

        <div class="row mb-3">
  <div class="col-md-12">
    <label for="address" class="form-label">Address</label>
    <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
  </div>
</div>
        <div class="row mb-3">
  <div class="col-md-6">
    <label for="gender" class="form-label">Gender</label>
    <select class="form-select" id="gender" name="gender" required>
      <option value="">Select Gender</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
      <option value="Other">Other</option>
    </select>
  </div>
</div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <small id="password_help" class="form-text text-muted">Password must be at least 8 characters, include an uppercase letter, a lowercase letter, a number, and a special character.</small>
          </div>
          <div class="col-md-6">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
          </div>
        </div>
        <div class="mb-3">
          <input type="checkbox" id="terms" name="terms" required>
          <label for="terms">I agree to the terms and conditions</label>
        </div>
        <div class="d-flex gap-3">
          <button type="submit" class="btn btn-primary">Sign Up</button>
          <button type="button" class="btn btn-secondary">Sign In</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.getElementById('confirm_password').addEventListener('input', function () {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    if (password !== confirmPassword) {
      this.setCustomValidity('Passwords do not match');
    } else {
      this.setCustomValidity(''); 
    }
  });

  document.getElementById('password').addEventListener('input', function () {
    const password = this.value;
    const passwordHelp = document.getElementById('password_help');
    const strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (!strongPassword.test(password)) {
      passwordHelp.style.color = 'red';
      this.setCustomValidity('Password does not meet the strength requirements');
    } else {
      passwordHelp.style.color = 'green';
      this.setCustomValidity('');
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
