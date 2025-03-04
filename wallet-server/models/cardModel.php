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

  //Get All Cards
  public static function getAllCards()
  {
    global $conn;
    $query = $conn->prepare("SELECT * FROM Cards ORDER BY id DESC");
    $query->execute();
    $result = $query->get_result();
    $cards = [];

    while ($card = $result->fetch_assoc()) {
      $cards[] = $card;
    }

    return json_encode($cards);
  }

  //Get Card BY ID
  public function getCardById($id)
  {
    if (empty($id)) {
      return responseError("Card ID is required");
    }

    $query = $this->conn->prepare("SELECT * FROM Cards WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $card = $result->fetch_assoc();

    if ($card) {
      return responseSuccess("Card found", $card);
    } else {
      return responseError("Card not found");
    }
  }

  // Update Card
  public function update($id, $wallet_id, $number, $pin, $expiry_date)
  {
    if (empty($id) || empty($wallet_id) || empty($number) || empty($pin) || empty($expiry_date)) {
      return responseError("Missing field is required to update");
    }

    $query = $this->conn->prepare("UPDATE Cards SET wallet_id = ?, number = ?, pin = ?, expiry_date = ? WHERE id = ?");

    $query->bind_param("isisi", $wallet_id, $number, $pin, $expiry_date, $id);
    $success = $query->execute();

    if ($success) {
      return responseSuccess("Card updated successfully");
    } else {
      return responseError("Failed to update Card");
    }
  }
}
