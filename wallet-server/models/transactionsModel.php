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

  // Get transaction by ID
  public function getTransactionById($id)
  {
    if (empty($id)) {
      die(responseError("Transaction ID is required"));
    }

    $query = $this->conn->prepare("SELECT * FROM transactions WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $transaction = $result->fetch_assoc();

    if ($transaction) {
      return responseSuccess("Transaction found", $transaction);
    } else {
      die(responseError("Transaction not found"));
    }
  }

  public function addTransaction($user_id, $sender_wallet_id, $recipient_wallet_id, $type, $amount, $card_number = null, $pin = null,)
  {
    if (empty($sender_wallet_id) || empty($type) || empty($amount)) {
      die(responseError("Missing fields"));
    }

    $wallet = new Wallet();
    $wallet_data = json_decode($wallet->getWalletById($sender_wallet_id));

    if ($wallet_data->status == "error") {
      die(responseError("Sender wallet not found"));
    }

    $current_balance = $wallet_data->data->balance;
    $new_balance = $current_balance;

    if ($type == 'deposit') {
      $new_balance += $amount;
      $recipient_wallet_id = $sender_wallet_id;
      $sender_wallet_id = 0;
    } elseif ($type == 'withdraw') {
      if (empty($card_number) || empty($pin)) {
        die(responseError("Card number and PIN are required for withdrawal"));
      }

      if ($current_balance < $amount) {
        die(responseError("Insufficient balance"));
      }

      $cardModel = new Card();
      if (!$cardModel->checkWalletCard($sender_wallet_id, $card_number, $pin)) {
        die(responseError("Invalid card number or PIN"));
      }

      $new_balance -= $amount;
    } elseif ($type == 'transfer') {
      if ($current_balance < $amount) {
        die(responseError("Insufficient balance for transfer"));
      }
      if (!$recipient_wallet_id) {
        die(responseError("Recipient wallet ID is required for transfer"));
      }
      $recipient_email = $recipient_wallet_id;
      $query = $this->conn->prepare("SELECT id FROM wallets WHERE user_id = (SELECT id FROM users WHERE email = ?) LIMIT 1");
      $query->bind_param("s", $recipient_email);
      $query->execute();
      $result = $query->get_result();

      if ($result->num_rows == 0) {
        die(responseError("Recipient email not found or has no wallet"));
      }

      $row = $result->fetch_assoc();
      $recipient_wallet_id = $row['id'];

      $recipient_wallet = json_decode($wallet->getWalletById($recipient_wallet_id));
      if ($recipient_wallet->status == "error") {
        die(responseError("Recipient wallet not found"));
      }

      $new_balance -= $amount;
      $recipient_new_balance = $recipient_wallet->data->balance + $amount;

      if (!$this->updateWalletBalance($recipient_wallet_id, $recipient_new_balance)) {
        die(responseError("Failed to update recipient wallet balance"));
      }
    }

    $walletToUpdate = 0;
    if ($sender_wallet_id !== 0) {
      $walletToUpdate = $sender_wallet_id;
    } else {
      $walletToUpdate = $recipient_wallet_id;
    }

    if (!$this->updateWalletBalance($walletToUpdate, $new_balance)) {
      die(responseError("Failed to update wallet balance"));
    }


    $query = $this->conn->prepare("INSERT INTO transactions (user_id,sender_wallet_id, type, amount, recipient_wallet_id) VALUES (?, ?, ?, ?, ?)");
    $query->bind_param("iisdi", $user_id, $sender_wallet_id, $type, $amount, $recipient_wallet_id);

    if ($query->execute()) {
      return responseSuccess("Transaction successful");
    } else {
      die(responseError("Transaction Failed"));
    }
  }

  // Update wallet Balance
  private function updateWalletBalance($wallet_id, $new_balance)
  {
    $query = $this->conn->prepare("UPDATE Wallets SET balance = ? WHERE id = ?");
    $query->bind_param("di", $new_balance, $wallet_id);
    return $query->execute();
  }

  // Get transactions by wallet ID
  public function getTransactionsByWalletId($wallet_id)
  {
    if (empty($wallet_id)) {
      die(responseError("Wallet ID is required"));
    }

    $query = $this->conn->prepare("SELECT * FROM transactions WHERE sender_wallet_id = ? OR recipient_wallet_id = ? ORDER BY id DESC");
    $query->bind_param("ii", $wallet_id, $wallet_id);
    $query->execute();
    $result = $query->get_result();

    $transactions = [];
    while ($transaction = $result->fetch_assoc()) {
      $transactions[] = $transaction;
    }

    return responseSuccess("Wallet Transactions", $transactions);
  }

  // Get transactions by User
  public function getUserTransactions($user_id)
  {
    if (empty($user_id)) {
      die(responseError("User ID is required"));
    }

    $query = $this->conn->prepare("
        SELECT * FROM transactions 
        WHERE sender_wallet_id IN (SELECT id FROM wallets WHERE user_id = ?) 
        OR recipient_wallet_id IN (SELECT id FROM wallets WHERE user_id = ?) 
        ORDER BY id DESC
    ");

    $query->bind_param("ii", $user_id, $user_id);
    $query->execute();
    $result = $query->get_result();


    $transactions = [];
    while ($transaction = $result->fetch_assoc()) {
      $transactions[] = $transaction;
    }

    return responseSuccess("User Transactions", $transactions);
  }
}
