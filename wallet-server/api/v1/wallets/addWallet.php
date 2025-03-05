<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/walletModel.php";


if (!isset($data["user_id"]) || !isset($data["name"])) {
    die(responseError("Missing email or password!"));
}

$wallet = new Wallet();
$response = $wallet->addWallet($data["user_id"], $data["name"]);

echo $response;
