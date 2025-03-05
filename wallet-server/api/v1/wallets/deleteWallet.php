<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/walletModel.php";


if (!isset($data["id"])) {
    die(responseError("Missing wallet ID"));
}

$wallet = new Wallet();
$response = $wallet->delete($data["id"]);
echo $response;
