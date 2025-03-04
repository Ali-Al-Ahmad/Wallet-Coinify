<?php
class Card
{
  private $conn;

  public function __construct()
  {
    global $conn;
    $this->conn = $conn;
  }

  //Card Add
  public function addCard($user_id, $wallet_id)
  {
    try {
      $query = $this->conn->prepare("SELECT phone FROM users WHERE id = ?");
      $query->bind_param("i", $user_id);
      $query->execute();
      $result = $query->get_result();
      $user = $result->fetch_assoc();

      $today = new DateTime();
      $today->modify('+10 days');
      $expiryDate = $today->format('Y-m-d');
      $query = $this->conn->prepare("INSERT INTO Cards (wallet_id, number, pin, expiry_date) VALUES (?, ?, ?,?)");
      $query->bind_param("isis", $wallet_id, $user["phone"], $wallet_id, $expiryDate);
      $success = $query->execute();

      return $success;
    } catch (error) {
      return false;
    }
  }
}
