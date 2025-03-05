<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/transactionsModel.php";


if (!isset($data["user_id"])) {
  die(responseError("User Id is required"));
}

$transaction = new Transaction();
$response = $transaction->getUserTransactions($data["user_id"]);
echo $response;
