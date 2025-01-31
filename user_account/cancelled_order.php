<?php
include '../db/connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your orders.");
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the logged-in user
$sql = "SELECT id, total_price, status, cancel_reason, admin_status, canceled_at, image 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY placed_on DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
    <h1 class="mb-4">My Orders</h1>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Image</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Cancel Reason</th>
                    <th>Admin Status</th>
                    <th>Canceled At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" width="100">
                        </td>
                        <td>₱<?php echo number_format((float)$row['total_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo $row['cancel_reason'] ? htmlspecialchars($row['cancel_reason']) : 'N/A'; ?></td>
                        <td><?php echo $row['admin_status'] ? htmlspecialchars($row['admin_status']) : 'Pending'; ?></td>
                        <td><?php echo $row['canceled_at'] ? htmlspecialchars($row['canceled_at']) : 'N/A'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">You have no orders to display.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
