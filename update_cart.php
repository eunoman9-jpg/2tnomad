<?php
session_start();

require_once "dbconn.php";
require_once "utils.php";

$db = new Database();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['action'])) { // $_SERVER['REQUEST_METHOD'] === 'POST'

    $id = $_POST['id'];
    $action = $_POST['action'];

    if (isset($_SESSION['cart'][$id])) {

        if ($action === 'increase') {
            // $_SESSION['cart'][$id]['quantity']++;
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
