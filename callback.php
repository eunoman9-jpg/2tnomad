<?php

$data = file_get_contents('php://input');
$log = file_put_contents('mpesa_log.json', $data, FILE_APPEND);

$response = json_decode($data);

$resultCode = $response->Body->stkCallback->ResultCode;

if ($resultCode == 0) {
    // SUCCESS
    $amount = $response->Body->stkCallback->CallbackMetadata->Item[0]->Value;
    $mpesaCode = $response->Body->stkCallback->CallbackMetadata->Item[1]->Value;
    $phone = $response->Body->stkCallback->CallbackMetadata->Item[4]->Value;

    // 👉 Save to DB (important)
    // mark order as PAID

} else {
    // FAILED
}
?>