<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$count = 0;
if (!empty($_SESSION['cart'])) {
    // $_SESSION['count'] = count($_SESSION['cart']); // Recalculate instead of reset
    $count = array_sum(array_column($_SESSION['cart'], 'quantity'));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2TNomad - Home</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="site">
        <main class="content">
            <!-- Navbar -->
            <nav class="navbar">
                <div class="logo">
                    <a href="index.php" id="nav-logo">2TNomad</a>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>


                    <li>
                        <a href="cart.php" class="cart-menu">
                            🛒 Cart (<span id="cart-count"><?php echo $count; ?></span>)
                        </a>
                    </li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <?php if (isset($_SESSION['user_id'])) { ?>
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