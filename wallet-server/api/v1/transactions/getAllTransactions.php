<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/transactionsModel.php";


//check if no id get All transactions
if (!isset($data["id"])) {

  $response = Transaction::getAllTransactions();
  echo $response;
  exit();
}

//get transaction by id if provided
$transaction = new Transaction();
$response = $transaction->gettransactionById($data["id"]);
echo $response;
