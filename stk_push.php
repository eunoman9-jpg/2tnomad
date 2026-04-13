<?php
require 'access_token.php';

$access_token = trim(ob_get_clean()); // get token output

$shortcode = "174379"; // sandbox default
$passkey = "";

$timestamp = date("YmdHis");
$password = base64_encode($shortcode . $passkey . $timestamp);

$phone = $_POST['phone']; // format: 2547XXXXXXXX
$amount = $_POST['amount'];

$url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

$callback_url = "https://yourdomain.com/callback.php";

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
?>