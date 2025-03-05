<?php
require_once("walletModel.php");
require_once("cardModel.php");

class Transaction
{
  private $conn;

  public function __construct()
  {
    global $conn;
    $this->conn = $conn;
  }

  // Get all transactions
  public static function getAllTransactions()
  {
    global $conn;
    $query = $conn->prepare("SELECT * FROM transactions ORDER BY id DESC");
    $query->execute();
    $result = $query->get_result();

    $transactions = [];
    while ($transaction = $result->fetch_assoc()) {
      $transactions[] = $transaction;
    }

    return responseSuccess("All transactions", $transactions);
  }
}
