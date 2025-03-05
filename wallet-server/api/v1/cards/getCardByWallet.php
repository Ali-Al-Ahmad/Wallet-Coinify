<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/cardModel.php";


if (!isset($data["wallet_id"])) {
  die(responseError("Wallet Id missing"));
}

$card = new Card();
$response = $card->getCardByWallet($data["wallet_id"]);

echo $response;
