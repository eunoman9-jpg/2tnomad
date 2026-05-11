<?php
session_start();

require_once "dbconn.php";

$db = new Database();

// Initialize cart if not already done
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// POST request handler for adding a product to the cart

if (isset($_POST['add_to_cart'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    if (isset($_SESSION['cart'][$id])) {
        // Increase quantity if already exists
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        // Add new product to cart with its quantity set to 1
        $_SESSION['cart'][$id] = [
            "id" => $id,
            "name" => $name,
            "price" => $price,
            "quantity" => 1
        ];
    }

    // Synchronize the count in case it needs to be shown elsewhere in the UI
    $count = array_sum(array_column($_SESSION['cart'], 'quantity'));

    echo json_encode([
        'status' => 'success',
        'count' => $count
    ]);
}


// GET request handler for increasing product quantity from cart page
if (isset($_GET['increase'])) {
    $id = $_GET['increase'];

    if (isset($_SESSION['cart'][$id])) {
        $stmt = $db->conn->prepare("SELECT stock FROM products WHERE product_id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $product = $result->fetch_assoc();
            $available_stock = $product['stock'];
        }

        if ($_SESSION['cart'][$id]['quantity'] < $available_stock) {
            $_SESSION['cart'][$id]['quantity']++;
        }

        $count = array_sum(array_column($_SESSION['cart'], 'quantity'));
    }
}

// GET request handler for decreasing product quantity from cart page
if (isset($_GET['decrease'])) {
    $id = $_GET['decrease'];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']--;

        // Remove the product from session storage when its quantity gets to 0
        if ($_SESSION['cart'][$id]['quantity'] <= 0) {
            unset($_SESSION['cart'][$id]);
        }

        $count/*$_SESSION['count']*/ = array_sum(array_column($_SESSION['cart'], 'quantity'));
    }
}

// GET request handler for completely removing a product from the cart
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    // $_SESSION['count']--;

    $count/*$_SESSION['count']*/ = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count/*$_SESSION['count']*/ += $item['quantity'];
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'count' => $count/*$_SESSION['count']*/]);
    exit();
}


include_once("./include/header.php");

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    // print_r($_SESSION['cart']);
    $total += $item['quantity'] * (float)$item['price'];
}
?>

<section class="cart">
    <h1>Your Shopping Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <p class="empty">Your cart is empty.</p>
    <?php else: ?>

        <?php foreach ($_SESSION['cart'] as $index => $item): ?>
            <div class="cart-item">
                <div>
                    <span><?php echo $item['name']; ?> - KES <?php echo $item['price']; ?></span>
                </div>
                <div class="cart-actions">
                    <div class="quantity-controls">
                        <a href="cart.php?decrease=<?php echo $index; ?>" class="qty-btn">−</a>
                        <p class="qty-value"><?php echo $item['quantity']; ?></p>
                        <a href="cart.php?increase=<?php echo $index; ?>" class="qty-btn">+</a>
                    </div>
                    <button class="remove-btn" data-id="<?php echo $index; ?>">Remove</button>
                </div>
            </div>
        <?php endforeach; ?>

        <h2 class="total">Total: KES <?php echo $total; ?></h2>
        <button class="checkout-btn" onclick="openCheckoutModal()">Checkout</button>

    <?php endif; ?>

    <div id="checkout-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeCheckoutModal()">&times;</span>
            <h2>Delivery Details</h2>

            <form id="checkout-form">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="phone" placeholder="M-Pesa Phone (2547XXXXXXXX)" id="phone" required>
                <input type="text" name="address" placeholder="Delivery Address" required>
                <input type="hidden" name="amount" value="<?php echo $total; ?>">
                <label for="optional">Optional Information</label>
                <input type="text" name="optional" id="optional" placeholder="Input any additional information">
                <?php
                if (isset($_SESSION["user_id"])) {
                ?>
                    <button class="confirm-pay" type="submit">Confirm & Pay</button>
                <?php
                } else {
                ?>
                    <a href="./login.php" class="login-text">login to confirm payment</a>
                <?php
                }
                ?>
            </form>
        </div>
    </div>
</section>

<script>
    function openCheckoutModal() {
        document.getElementById('checkout-modal').style.display = 'block';
    }

    function closeCheckoutModal() {
        document.getElementById('checkout-modal').style.display = 'none';
    }

    // Submit form → send STK push
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        const phone = document.getElementById("phone").value;
        console.log(phone)

        const regex = /^254\d{9}$/;

        if (regex.test(phone)) {
            console.log("Valid phone number");
            fetch('stk_push.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    alert("STK Push sent. Check your phone.");
                    window.location.href = "cart.php?clear=true";
                })
                .catch(err => {
                    alert("Something went wrong. Try again.");
                    console.error(err);
                });
        } else {
            alert("Invalid phone number");
        }


    });

    // Close when clicking outside
    window.onclick = function(event) {
        let modal = document.getElementById('checkout-modal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Remove product from the cart via AJAX
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const itemId = this.dataset.id;

            fetch(`cart.php?remove=${itemId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove item from DOM
                        this.closest('.cart-item').remove();

                        // Update cart counter in navbar
                        document.getElementById('cart-count').textContent = data.count;

                        // Reload page to recalculate total
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>

<script src="./script.js"></script>

<?php include_once("include/footer.php"); ?>