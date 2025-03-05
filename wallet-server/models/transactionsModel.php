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

  // Delete transaction
  public function delete($id)
  {
    if (empty($id)) {
      die(responseError("Transaction ID is missing"));
    }

    $query = $this->conn->prepare("DELETE FROM transactions WHERE id = ?");
    $query->bind_param("i", $id);

    if ($query->execute()) {
      return responseSuccess("Transaction deleted successfully");
    } else {
      die(responseError("Failed to delete transaction"));
    }
  }
}
