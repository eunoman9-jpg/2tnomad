<?php

$consumerKey = "U9X6RylAsNFYBuvTfzu6Fyd6cS5NRoUq3qbaDUnX9QRSZYL5";
$consumerSecret = "9cLQbqOGCOOrveTC8ZDTGJbjALYmofM2U2OqJ5sIGYPKFeWADEhrrbTLjCCLIWNk";

$url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";

$credentials = base64_encode($consumerKey . ":" . $consumerSecret);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic " . $credentials
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response);

echo $data->access_token;
?>