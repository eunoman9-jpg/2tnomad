<?php
session_start();

require 'dbconn.php';

$db = new Database();

if (!isset($_GET['product_id'])) {
    die("Product not found");
}

$id = intval($_GET['product_id']);

$query = "SELECT * FROM products WHERE product_id = $id LIMIT 1";
$result = $db->conn->query($query);

if (!$result || $result->num_rows === 0) {
    die("Product not found");
}

$product = $result->fetch_assoc();

include_once("./include/header.php");
?>

<div class="product-details">
    <div class="product-image">
        <img src="<?php echo $product['image_url']; ?>" alt="">
    </div>

    <div class="product-info">
        <h1><?php echo $product['name']; ?></h1>
        <p class="price">KES <?php echo $product['price']; ?></p>

        <p class="description">
            <?php echo $product['description'] ?? 'No description available.'; ?>
        </p>
        <div class="form-control"
            data-id="<?php echo $product['product_id']; ?>"
            data-name="<?php echo htmlspecialchars($product['name']); ?>"
            data-price="<?php echo $product['price']; ?>">

            <?php
            $cart = $_SESSION['cart'] ?? [];
            $product_id = $product['product_id'];

            if (isset($cart[$product_id])) {
                $qty = $cart[$product_id]['quantity'];
            ?>

                <div class="quantity-controls" data-id="<?php echo $product_id; ?>">
                    <button class="qty-btn minus">−</button>
                    <span class="qty-value"><?php echo $qty; ?></span>
                    <button class="qty-btn plus">+</button>
                </div>

            <?php } else { ?>

                <button class="add-to-cart-btn">Add to Cart</button>

            <?php } ?>
        </div>
        <!-- <button
            class="add-to-cart-btn"
            data-id="<?php echo $product['product_id']; ?>"
            data-name="<?php echo $product['name']; ?>"
            data-price="<?php echo $product['price']; ?>">
            Add to Cart
        </button> -->
    </div>
</div>
<script src="./script.js"></script>

<?php include_once("include/footer.php"); ?>
