<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/walletModel.php";


if (!isset($data["user_id"])) {
  die(responseError("User Id missing"));
}

$wallet = new wallet();
$response = $wallet->getAllWalletsByUserId($data["user_id"]);

echo $response;
