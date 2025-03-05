<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/walletModel.php";


if (!isset($data["id"]) || !isset($data["user_id"]) || !isset($data["name"]) || !isset($data["balance"])) {
    die(responseError("Missin fields"));
}

$wallet = new Wallet();
$response = $wallet->update($data["id"], $data["user_id"], $data["name"], $data["balance"]);
echo $response;
