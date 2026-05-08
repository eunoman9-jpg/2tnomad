<?php
session_start();

require_once "utils.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_POST['add_to_cart'])) { // $_SERVER['REQUEST_METHOD'] === 'POST'
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        $_SESSION['cart'][$id] = [
            'name' => $name,
            'price' => $price,
            'quantity' => 1
        ];
    }

    $count = getCartCount($_SESSION['cart']);

    echo json_encode([
        'status' => 'success',
        'count' => $count
    ]);
}
?>