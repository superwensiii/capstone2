

<?php
include 'db/connect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = intval($_GET['id']);

    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid product ID.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Section</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .product-section {
      background-color: whitesmoke;
      padding: 30px;
      border-radius: 10px;
    }
    .product-image img {
      width: 100%;
      border-radius: 10px;
    }
    .star-rating i {
      color: #ffc107;
    }
    .btn-buy-now, .btn-add-to-cart {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .btn-buy-now {
      background-color: #007bff;
      color: #fff;
    }
    .btn-buy-now:hover {
      background-color: #0056b3;
    }
    .btn-add-to-cart {
      background-color: #28a745;
      color: #fff;
    }
    .btn-add-to-cart:hover {
      background-color: #218838;
    }
    .btn-icon {
      margin-right: 8px;
    }
    .reviews-section {
      margin-top: 40px;
      padding: 20px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .review {
      margin-bottom: 20px;
    }
    .review p {
      margin: 0;
    }
    .review .reviewer-name {
      font-weight: bold;
    }
    .review .review-date {
      color: #888;
      font-size: 0.9rem;
    }

    .small-image {
  width: 60px;
  height: 100px;
  cursor: pointer;
  border: 1px solid #ddd;
  object-fit: cover;
  transition: transform 0.2s ease;
}

.small-image:hover {
  transform: scale(1.1);
  border-color: transparent;
}
  </style>
  <title><?php echo htmlspecialchars($product['product_name']); ?> - Quick View</title>
</head>
<body>
<?php 
     include 'navbar.php'; 
     ?>
  <div class="container my-5">
    <div class="row product-section">
        <!-- Product Image -->
        <div class="col-md-5 product-image">
            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="img-fluid main-image" id="mainImage">
            <div class="mt-3 d-flex gap-2">
                <!-- Placeholder for additional images -->
                <img src="images/2nd.png" alt="Side Angle" class="small-image">
                <img src="images/1st.png" alt="Back Angle" class="small-image">
                <img src="images/3rd.png" alt="Top Angle" class="small-image">
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-7">
            <h3 class="fw-bold"><?php echo htmlspecialchars($product['product_name']); ?></h3>
            <p class="text-muted"><?php echo htmlspecialchars($product['product_description']); ?></p>
            <p>
                <span class="fs-4 fw-bold text-dark">₱<?php echo number_format($product['discounted_price'], 2); ?></span>
                <del class="text-muted">₱<?php echo number_format($product['original_price'], 2); ?></del>
                <span class="badge bg-success"><?php echo $product['discount_percentage']; ?>% OFF</span>
            </p>
            <!-- Star Rating -->
            <div class="star-rating mb-3">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-half"></i>
                <span class="text-muted">(4.5/5)</span>
            </div>
            <!-- Delivery Time -->
            <p class="text-muted"><i class="bi bi-truck"></i> Delivery in 3-5 business days</p>
            <!-- Select Options -->
            <form>
                <div class="mb-3">
                    <label for="color" class="form-label">Color:</label>
                    <select class="form-select" id="color" name="color">
                        <option selected>Select a color</option>
                        <option value="black">Black</option>
                        <option value="white">White</option>
                        <option value="red">Red</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1">
                </div>
                <!-- Buttons -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-warning flex-grow-1">
                        <i class="bi bi-cart-plus btn-icon" a href="cart.php"></i>Add to Cart
                    </button>
                    <button type="button" class="btn btn-dark flex-grow-1">
                        <i class="bi bi-bag-check btn-icon" a href="checkout.php"></i>Checkout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="path/to/bootstrap.bundle.js"></script>

    <!-- Customer Reviews Section -->
    <div class="reviews-section mt-5">
      <h4 class="fw-bold text-center">Customer Reviews</h4>
      <div class="review">
        <p class="reviewer-name">John Doe</p>
        <p class="review-date">Reviewed on Nov 30, 2024</p>
        <div class="star-rating">
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-half"></i>
        </div>
        <p>"Excellent product! Works perfectly and the delivery was on time."</p>
      </div>
      <div class="review">
        <p class="reviewer-name">Jane Smith</p>
        <p class="review-date">Reviewed on Nov 28, 2024</p>
        <div class="star-rating">
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star"></i>
          <i class="bi bi-star"></i>
        </div>
        <p>"Good product, but it could have a bit more capacity."</p>
      </div>
    </div>
  </div>






  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
