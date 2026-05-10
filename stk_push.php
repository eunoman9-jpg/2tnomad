<?php
session_start();

require_once 'dbconn.php';
require_once 'access_token.php';

$access_token = trim(ob_get_clean()); // get token output

$db = new Database();

$shortcode = "174379"; // sandbox default
$passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";

$timestamp = date("YmdHis");
$password = base64_encode($shortcode . $passkey . $timestamp);

$phone = $_POST['phone']; // format: 2547XXXXXXXX
$amount = $_POST['amount'];

$url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

$callback_url = "https://extradite-overtime-contend.ngrok-free.dev/2tnomad/callback.php";

$data = [
    "BusinessShortCode" => $shortcode,
    "Password" => $password,
    "Timestamp" => $timestamp,
    "TransactionType" => "CustomerPayBillOnline",
    "Amount" => $amount,
    "PartyA" => $phone,
    "PartyB" => $shortcode,
    "PhoneNumber" => $phone,
    "CallBackURL" => $callback_url,
    "AccountReference" => "Order123",
    "TransactionDesc" => "Payment for items"
];

foreach ($_SESSION['cart'] as $index => $item):
    $query = $db->conn->prepare("SELECT stock FROM products WHERE product_id = ?");
    $query->bind_param("s", $item['id']);
    $query->execute();

    // Get result set
    $result = $query->get_result();
    $stock = 0;

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
        $stock = (int)$product['stock'];
    }

    // Free result and close statement before running another query
    $result->free();
    $query->close();    

    $new_stock = $stock - (int)$item['quantity'];
    $stmt = $db->conn->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
    $stmt->bind_param("is", $new_stock, $item['id']);
    $stmt->execute();
    $stmt->close();

    $db->conn->commit();
endforeach;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $access_token,
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

echo $response;
