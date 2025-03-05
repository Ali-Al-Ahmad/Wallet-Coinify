<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/transactionsModel.php";


if (!isset($data["user_id"]) || !isset($data["sender_wallet_id"]) || !isset($data["type"]) || !isset($data["amount"])) {
  die(responseError("Missing Fields!"));
}

$card_number = isset($data["card_number"]) ? $data["card_number"] : null;
$pin = isset($data["pin"]) ? $data["pin"] : null;
$recipient_wallet_id = isset($data["recipient_wallet_id"]) ? $data["recipient_wallet_id"] : 0;

$transaction = new Transaction();
$response = $transaction->addTransaction($data["user_id"], $data["sender_wallet_id"], $recipient_wallet_id, $data["type"], $data["amount"], $card_number, $pin);

echo $response;
