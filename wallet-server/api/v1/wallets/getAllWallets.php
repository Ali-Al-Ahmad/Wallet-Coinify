<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/walletModel.php";


//check if no id get All wallets
if (!isset($data["id"])) {

  $response = Wallet::getAllwallets();
  echo $response;
  exit();
}

//get wallet by id if provided
$wallet = new Wallet();
$response = $wallet->getwalletById($data["id"]);
echo $response;
