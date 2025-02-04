<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://www.paypal.com/sdk/js?client-id=AfWWgIuFSgyu8PBCPZaSblbJ4tuRBURmBDp3lGvNAqcyJmX5zn84vfiPbbEgTviDvsI7kkHQqMSaxYcY"></script>
</head>
<body>
<?php
include 'navbar.php';
include 'db/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once('vendor/autoload.php');

$client = new \GuzzleHttp\Client();

$response = $client->request('POST', 'https://api.paymongo.com/v1/links', [
  'body' => '{"data":{"attributes":{"amount":1000000,"description":"Purchase From Great Wall Arts","remarks":"Purchase form Great Wall Arts"}}}',
  'headers' => [
    'accept' => 'application/json',
    'authorization' => 'Basic c2tfdGVzdF90Yk1qRG1qZWVvWDRrQ3lQVzRZc0NqWU46',
    'content-type' => 'application/json',
  ],
]);
 // echo $response->getBody(); 

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT first_name, surname, email, phone, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch cart items
$cart = [];
$cart_query = $conn->prepare("SELECT * FROM cart_items WHERE user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();
while ($row = $cart_result->fetch_assoc()) {
    $cart[] = $row;
}
$cart_query->close();

if (empty($cart)) {
    echo "<div class='alert alert-danger'>Your cart is empty! <a href='index.php'>Go back to the cart</a>.</div>";
    exit;
}

// Calculate totals
$merchandiseSubtotal = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $cart));
$shippingSubtotal = 40;
$voucherDiscount = 15;
$totalPayment = $merchandiseSubtotal + $shippingSubtotal - $voucherDiscount;

// Handle order placement
$orderSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $voucher_used = "10% Off"; // Example voucher
    $discount = $merchandiseSubtotal * 0.10; // Calculate 10% discount
    $placed_on = date("Y-m-d H:i:s");
    $status = "Pending";

    foreach ($cart as $item) {
        // Calculate fields for binding
        $name = $user['first_name'] . " " . $user['surname'];
        $number = $user['phone'];
        $email = $user['email'];
        $address = $user['address'];
        $payment_method = "Cash on Delivery"; // Replace with actual payment method
        $message = ""; // Add user message or leave empty
        $product_name = $item['product_name'];
        $quantity = $item['quantity'];
        $total_price = $item['price'] * $item['quantity']; // Total price for the product
        $image = $item['image'];

        // Prepare the query
        $insert_order = $conn->prepare("
            INSERT INTO orders 
            (user_id, name, product_name, number, email, address, payment_method, voucher_used, total_products, total_price, placed_on, status, message, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        // Bind the variables
        $insert_order->bind_param(
            "issssssissssss",
            $user_id,
            $name,
            $product_name,
            $number,
            $email,
            $address,
            $payment_method,
            $voucher_used,
            $quantity, // Fixed: Use variable for quantity
            $total_price, // Fixed: Use variable for total price
            $placed_on,
            $status,
            $message,
            $image
        );

        // Execute the query
        if (!$insert_order->execute()) {
            echo "<div class='alert alert-danger'>Error placing order for product {$product_name}: " . $insert_order->error . "</div>";
        }

        $insert_order->close();
    }

    // Clear the user's cart after inserting all products
    $clear_cart = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $clear_cart->bind_param("i", $user_id);
    $clear_cart->execute();
    $clear_cart->close();

    $orderSuccess = true; // Set success flag
}

?>
<div class="container my-5">
    <?php if ($orderSuccess): ?>
        <div class="alert alert-success text-center">
            <h2 class="text-dark">Order Successfully Placed!</h2>
            <p class="text-center">Thank you for shopping with us. Your order is now being processed.</p>
            <a href="index.php" class="btn btn-dark">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-map-marker-alt"></i> Delivery Address</h5>
                <p class="card-text">
                    <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['surname']); ?> 
                    (<?php echo htmlspecialchars($user['phone']); ?>)</strong><br>
                    <?php echo nl2br(htmlspecialchars($user['address'])); ?>
                </p>
                <a href="edit_account.php" class="btn btn-dark btn-sm">Edit</a>
            </div>
        </div>

        <h1>Checkout</h1>
        <form method="post">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($item['image']); ?>" style="width: 80px;"></td>
                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title text-center">Choose Payment Method</h5>
        <div class="row"> <!-- Use Bootstrap's grid system -->
            <!-- PayMongo Button -->
            <div class="col-md-4 mb-3"> <!-- Column for PayMongo -->
                <button id="paymongo-button" class="btn btn-dark text-white w-100">
                    Payment Center / E-Wallet / Online Banking
                </button>
            </div>

            <!-- PayPal -->
            <div class="col-md-4 mb-3"> <!-- Column for PayPal -->
                <div id="paypal-button-container" class="w-100"></div>
            </div>

            <!-- Cash on Delivery -->
            <div class="col-md-4 mb-3"> <!-- Column for COD -->
                <button class="btn btn-dark w-100">
                    Cash on Delivery (COD)
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript for handling PayMongo payment button
    document.getElementById('paymongo-button').addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission or button behavior

        // Open the PayMongo payment link in a new window
        const paymongoLink = "https://pm.link/org-NNy9SKwLXZ6sU49dURUX9Hq7/test/W7FBit2"; // Replace with your PayMongo payment link
        window.open(paymongoLink, '_blank'); // Open in a new tab/window
    });

    // Initialize PayPal button
    paypal.Buttons().render('#paypal-button-container');
</script>

<script>
  

   
        // Call the PayMongo API using Fetch API to create a payment link
        
    // Example: PayMongo payment link creation
    fetch('https://api.paymongo.com/v1/links', {
    method: 'POST',
    headers: {
        'Authorization': 'Basic sk_test_tbMjDmjeeoX4kCyPW4YsCjYN' ,
        'Content-Type': 'application/json',
        
        
    },
    body: JSON.stringify({
        data: {
            attributes: {
                amount: <?php echo $totalPayment * 100; ?>,
                description: 'Order Payment for <?php echo htmlspecialchars($user["first_name"] . " " . $user["surname"]); ?>',
                remarks: 'Order Checkout'
            }
        }
    })
})
.then(response => response.json())
.then(data => {
    console.log('Response:', data); // Debug: See the full API response
    if (data.data && data.data.attributes.checkout_url) {
        console.log('Redirecting to:', data.data.attributes.checkout_url); // Debug: Log the URL
        window.location.href = data.data.attributes.checkout_url; // Redirect to the payment page
    } else {
        console.error('Error: Invalid response from PayMongo API');
        alert('Error creating PayMongo payment link. Please try again.');
    }
})
.catch(error => {
    console.error('Error:', error);
    alert('Failed to connect to PayMongo. Please check your API key or internet connection.');
});


          
    
   
    
</script>




<div class="mb-4">
    <label for="message" class="form-label">Message us!</label>
    <input type="text" class="form-control" id="message" placeholder="Please leave a message...">
</div>


            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Payment Summary</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Merchandise Subtotal:</span>
                        <span>₱<?php echo number_format($merchandiseSubtotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Shipping Subtotal:</span>
                        <span>₱<?php echo number_format($shippingSubtotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Voucher Discount:</span>
                        <span>-₱<?php echo number_format($voucherDiscount, 2); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Total Payment:</strong>
                        <strong>₱<?php echo number_format($totalPayment, 2); ?></strong>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
  <a href="cart.php" class="btn btn-dark me-2">Back to Cart</a>
  <button type="submit" name="place_order" class="btn btn-warning">Place Order</button>
</div>

        </form>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
