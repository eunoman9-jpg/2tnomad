<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "utils.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$count = getCartCount($_SESSION['cart']);;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2TNomad - Home</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="site">
        <main class="content">
            <!-- Navbar -->
            <nav class="navbar">
                <div class="logo">
                    <a href="index.php" id="nav-logo">2TNomad</a>
                </div>

                <!-- Hamburger button -->
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <ul class="nav-links" id="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li>
                        <a href="cart.php" class="cart-menu">
                            🛒 Cart (<span id="cart-count"><?php echo $count; ?></span>)
                        </a>
                    </li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <?php if (isset($_SESSION['user_id'])) {
                        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == "admin") {
                    ?>
                            <li>
                                <a href="admin.php">Site Administration</a>
                            </li>
                        <?php
                        }
                        ?>
                        <li class="dropdown">
                            <button class="dropdown-btn"><?php echo $_SESSION['user_name']; ?> ▼</button>
                            <div class="dropdown-menu">
                                <a href="orders.php">Orders</a>
                                <a href="logout.php">Logout</a>
                            </div>
                        </li>
                    <?php } else { ?>
                        <li><a href="login.php">Account</a></li>
                    <?php } ?>


                </ul>
            </nav>