<?php
session_start();

require_once "utils.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['action'])) { // $_SERVER['REQUEST_METHOD'] === 'POST'

    $id = $_POST['id'];
    $action = $_POST['action'];

    if (isset($_SESSION['cart'][$id])) {

        if ($action === 'increase') {
            $_SESSION['cart'][$id]['quantity']++;
        }

        if ($action === 'decrease') {
            $_SESSION['cart'][$id]['quantity']--;

            if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }
    }

    // total count
    // $count = array_sum(array_column($_SESSION['cart'], 'quantity'));
    $count = getCartCount($_SESSION['cart']);

    // return updated quantity
    $qty = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id]['quantity'] : 0;

    echo json_encode([
        'status' => 'success',
        'count' => $count,
        'quantity' => $qty
    ]);
}
