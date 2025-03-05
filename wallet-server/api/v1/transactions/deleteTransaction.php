<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/transactionsModel.php";


if (!isset($data["id"])) {
  die(responseError("Missing transaction ID"));
}

$transaction = new Transaction();
$response = $transaction->delete($data["id"]);
echo $response;
