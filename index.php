<?php
session_start();

require_once "dbconn.php";
require_once "utils.php";

$db = new Database();

// Retrieve all the products from the database
$result = $db->conn->query("SELECT * FROM products ORDER BY created_at DESC");

// Close connection when done
$db->close();


include_once("./include/header.php");
?>



<!-- Hero Section -->
<section class="hero">
    <h1>Karibu kwa Vespahub</h1>
    <p>Premium Vespa PX Accessories for Enthusiasts</p>

</section>

<!-- Featured Products -->
<section class="featured">
    <h2>Featured Products</h2>
    <div class="products-container">
        <?php
        if ($result) {
            while ($row = $result->fetch_assoc()) {
        ?>

                <div class="product-card">

                    <!-- Clickable area -->
                    <a class="product-details-link" href="product.php?product_id=<?php echo $row['product_id']; ?>">
                        <img class="product_image" src="<?php echo $row["image_url"]; ?>" alt="<?php echo $row['name']; ?>">
                        <h3><?php echo truncateText($row['name']); ?></h3>
                        <p>KES <?php echo $row["price"]; ?></p>
                    </a>

                    <!-- Button OUTSIDE the link -->
                    <div class="form-control"
                        data-id="<?php echo $row['product_id']; ?>"
                        data-name="<?php echo htmlspecialchars($row['name']); ?>"
                        data-price="<?php echo $row['price']; ?>">
                        <?php
                        $product_id = $row['product_id'];
                        $cart = $_SESSION['cart'] ?? [];

                        if (isset($cart[$product_id])) {
                            $qty = $cart[$product_id]['quantity'];
                        ?>

                            <!-- Quantity controls -->
                            <div class="quantity-controls" data-id="<?php echo $product_id; ?>">
                                <button class="qty-btn minus">−</button>
                                <span class="qty-value"><?php echo $qty; ?></span>
                                <button class="qty-btn plus">+</button>
                            </div>

                        <?php } else { ?>
                            <button class="add-to-cart-btn">
                                Add to Cart
                            </button>
                        <?php } ?>
                    </div>

                </div>
                <!-- </a> -->
        <?php
            }
        } else {
            echo "Query failed: " . $db->conn->error;
        }
        ?>

    </div>
</section>

<script src="./script.js"></script>

<?php include_once('include/footer.php'); ?>
