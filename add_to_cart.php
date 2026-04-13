<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Count total items
    $totalItems = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalItems += 1;
    }

    echo json_encode([
        'status' => 'success',
        'count' => $totalItems
    ]);
}
?>