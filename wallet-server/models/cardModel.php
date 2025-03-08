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
    $query = $conn->prepare("SELECT * FROM cards ORDER BY id DESC");
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

    $query = $this->conn->prepare("SELECT * FROM cards WHERE id = ?");
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

  // Delete Card
  public function delete($id)
  {
    if (empty($id)) {
      return responseError("Card ID is missing");
    }

    $query = $this->conn->prepare("DELETE FROM cards WHERE id = ?");
    $query->bind_param("i", $id);
    $success = $query->execute();

    if ($success) {
      return responseSuccess("Card deleted successfully");
    } else {
      return responseError("Failed to delete Card");
    }
  }

  // Delete Card by WalletId
  public function deleteCardByWallet($wallet_id)
  {
    if (empty($wallet_id)) {
      return responseError("Card ID is missing");
    }

    $query = $this->conn->prepare("DELETE FROM cards WHERE wallet_id = ?");
    $query->bind_param("i", $wallet_id);
    $success = $query->execute();
    return $success;
  }

  // Delete All Cards by User
  public function deleteAllCardsByUser($user_id)
  {
    if (empty($wallet_id)) {
      return responseError("Card ID is missing");
    }

    $query = $this->conn->prepare("DELETE FROM cards WHERE wallet_id IN (SELECT id FROM wallets WHERE user_id = ?)");
    $query->bind_param("i", $user_id);
    $success = $query->execute();
    return $success;
  }

  //getCardbywallet
  public function getCardByWallet($wallet_id)
  {
    if (empty($wallet_id)) {
      return responseError("Wallet ID is required");
    }

    $query = $this->conn->prepare("SELECT * FROM cards WHERE wallet_id = ?");
    $query->bind_param("i", $wallet_id);
    $query->execute();
    $result = $query->get_result();
    $cards = [];

    while ($card = $result->fetch_assoc()) {
      $cards[] = $card;
    }

    return responseSuccess("Card", $cards);
  }

  //checkWalletCard

  public function checkWalletCard($wallet_id, $card_number, $pin)
  {
    if (empty($wallet_id) || empty($card_number) || empty($pin)) {
      return responseError("Missing field required");
    }
    $query = $this->conn->prepare("SELECT id,expiry_date FROM cards WHERE wallet_id = ? AND number = ? AND pin = ?");
    $query->bind_param("isi", $wallet_id, $card_number, $pin);
    $query->execute();
    $result = $query->get_result();

    $card = $result->fetch_assoc();

    if (!$card) {
      return false;
      exit();
    }

    $now = new DateTime();
    $card_date = new DateTime($card["expiry_date"]);
    if ($now > $card_date) {
      return false;
      exit();
    }

    return true;
  }
}
