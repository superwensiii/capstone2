<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php
// Include navbar and database connection
include 'navbar.php';
include 'db/connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch orders for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT product_name, image, payment_method, voucher_used, total_products, total_price, placed_on, status 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY placed_on DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="container my-4">
    <h1 class="mb-4">My Orders</h1>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span>
                        <strong>Placed On:</strong> 
                        <?php 
                        // Format date and time
                        echo date("M d, Y h:i A", strtotime($order['placed_on'])); 
                        ?>
                    </span>
                    <button class="btn btn-sm btn-secondary text-white" disabled>
    <?php 
    echo htmlspecialchars($order['status'] === 'Pending' ? 'Order Received' : $order['status']); 
    ?>
</button>

                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                    <div class="col-md-2 text-center">
    <?php 
    $image_path = htmlspecialchars($order['image']);

    // Ensure the image path is not empty
    if (!empty($image_path)) {
        // If the path is relative, prepend the base URL
        if (!filter_var($image_path, FILTER_VALIDATE_URL)) {
            $image_path = "http://localhost/capstone2/" . $image_path;
        }

        // Display the image
        echo '<img src="' . $image_path . '" alt="Product" class="img-fluid rounded">';
    } else {
        // Display a placeholder image if no image is found
        echo '<img src="images/placeholder.jpg" alt="Placeholder Image" class="img-fluid rounded">';
    }
    ?>
</div>

                        <div class="col-md-6">
                            <h5 class="card-title"><?php echo htmlspecialchars($order['product_name']); ?></h5>
                            <p class="text-muted mb-1"><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                            <p class="text-muted mb-0"><strong>Voucher Used:</strong> <?php echo htmlspecialchars($order['voucher_used'] ? $order['voucher_used'] : 'None'); ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h5 class="text-danger">₱<?php echo number_format($order['total_price'], 2); ?></h5>
                            <p class="text-muted mb-0"><strong>Total Products:</strong> <?php echo htmlspecialchars($order['total_products']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <h4>No Orders Found</h4>
            <p>Looks like you haven't placed any orders yet. <a href="index.php" class="btn btn-dark btn-sm">Shop Now</a></p>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
