<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e9ecef;
            font-size: 1rem;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .text-muted {
            font-size: 0.9rem;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .btn-warning {
            background-color: #ffc107;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .btn-dark {
            background-color: #343a40;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .modal-content {
            border-radius: 10px;
        }
        .modal-header {
            border-bottom: 1px solid #e9ecef;
        }
        .modal-footer {
            border-top: 1px solid #e9ecef;
        }
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<?php
// Include navbar and database connection
include '../navbar.php';
include '../db/connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch orders for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT id, product_name, image, payment_method, voucher_used, total_products, total_price, placed_on, status 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY placed_on DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="container my-5">
    <h1 class="mb-4 text-center">My Orders</h1>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <strong>Placed On:</strong> 
                        <?php echo date("M d, Y h:i A", strtotime($order['placed_on'])); ?>
                    </span>
                    <button class="btn btn-sm btn-secondary" disabled>
                        <?php echo htmlspecialchars($order['status'] === 'Pending' ? 'Order Received' : $order['status']); ?>
                    </button>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <?php 
                            $image_path = htmlspecialchars($order['image']);
                            if (!empty($image_path)) {
                                if (!filter_var($image_path, FILTER_VALIDATE_URL)) {
                                    $image_path = "http://localhost/capstone2/" . $image_path;
                                }
                                echo '<img src="' . $image_path . '" alt="Product" class="img-fluid rounded" style="max-height: 100px;">';
                            } else {
                                echo '<img src="images/placeholder.jpg" alt="Placeholder Image" class="img-fluid rounded" style="max-height: 100px;">';
                            }
                            ?>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title"><?php echo htmlspecialchars($order['product_name']); ?></h5>
                            <p class="text-muted mb-1"><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                            <p class="text-muted mb-0"><strong>Voucher Used:</strong> <?php echo htmlspecialchars($order['voucher_used'] ?: 'None'); ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h5 class="text-danger">₱<?php echo number_format((float)$order['total_price'], 2); ?></h5>
                            <p class="text-muted mb-0"><strong>Total Products:</strong> <?php echo htmlspecialchars($order['total_products']); ?></p>
                            
                            <button type="button" class="btn btn-warning mt-2 view-details" data-order='<?php echo json_encode($order); ?>'>
                                View Details
                            </button>
                            <button type="button" class="btn btn-dark mt-2 contact-us">
                                Contact Us
                            </button>
                            <button type="button" class="btn btn-danger mt-2 cancel-order" data-order-id="<?php echo $order['id']; ?>">
                                Cancel Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <h4>No Orders Found</h4>
            <p>Looks like you haven't placed any orders yet. <a href="../index.php" class="btn btn-dark btn-sm">Shop Now</a></p>
        </div>
    <?php endif; ?>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="cancel_order.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="cancelOrderId">
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">Reason for Cancellation</label>
                        <textarea name="cancel_reason" id="cancelReason" class="form-control" rows="3" required placeholder="Provide your reason here..."></textarea>
                    </div>
                    <p class="text-muted">
                        <small>
                            By canceling, you agree to our cancellation policy. Refunds may take 3-5 business days to process, and some restrictions may apply.
                        </small>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img id="detailImage" src="" alt="Product Image" class="img-fluid rounded" style="max-height: 200px;">
                    </div>
                    <div class="col-md-8">
                        <h5 id="detailProductName" class="card-title"></h5>
                        <p class="text-muted mb-1"><strong>Payment Method:</strong> <span id="detailPaymentMethod"></span></p>
                        <p class="text-muted mb-1"><strong>Voucher Used:</strong> <span id="detailVoucherUsed"></span></p>
                        <p class="text-muted mb-1"><strong>Total Products:</strong> <span id="detailTotalProducts"></span></p>
                        <p class="text-muted mb-1"><strong>Total Price:</strong> <span id="detailTotalPrice"></span></p>
                        <p class="text-muted mb-1"><strong>Placed On:</strong> <span id="detailPlacedOn"></span></p>
                        <p class="text-muted mb-1"><strong>Status:</strong> <span id="detailStatus"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Contact Us Modal -->
<div class="modal fade" id="contactUsModal" tabindex="-1" aria-labelledby="contactUsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="contact_us.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactUsModalLabel">Contact Us</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="contactMessage" class="form-label">Your Message</label>
                        <textarea name="message" id="contactMessage" class="form-control" rows="5" required placeholder="Enter your message here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-dark">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cancel Order Modal
    const cancelButtons = document.querySelectorAll('.cancel-order');
    const cancelOrderModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
    const cancelOrderIdInput = document.getElementById('cancelOrderId');

    cancelButtons.forEach(button => {
        button.addEventListener('click', function () {
            const orderId = this.getAttribute('data-order-id');
            cancelOrderIdInput.value = orderId;
            cancelOrderModal.show();
        });
    });

    // View Details Modal
    const viewDetailsButtons = document.querySelectorAll('.view-details');
    const viewDetailsModal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));

    viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function () {
            const order = JSON.parse(this.getAttribute('data-order'));
            document.getElementById('detailImage').src = order.image.startsWith('http') ? order.image : `http://localhost/capstone2/${order.image}`;
            document.getElementById('detailProductName').textContent = order.product_name;
            document.getElementById('detailPaymentMethod').textContent = order.payment_method;
            document.getElementById('detailVoucherUsed').textContent = order.voucher_used || 'None';
            document.getElementById('detailTotalProducts').textContent = order.total_products;
            document.getElementById('detailTotalPrice').textContent = `₱${parseFloat(order.total_price).toFixed(2)}`;
            document.getElementById('detailPlacedOn').textContent = new Date(order.placed_on).toLocaleString();
            document.getElementById('detailStatus').textContent = order.status;
            viewDetailsModal.show();
        });
    });

    // Contact Us Modal
    const contactUsButtons = document.querySelectorAll('.contact-us');
    const contactUsModal = new bootstrap.Modal(document.getElementById('contactUsModal'));

    contactUsButtons.forEach(button => {
        button.addEventListener('click', function () {
            contactUsModal.show();
        });
    });
});
</script>
</body>
</html>