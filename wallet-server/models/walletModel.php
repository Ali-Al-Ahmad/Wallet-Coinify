<?php
require_once "cardModel.php";

class Wallet
{
  private $conn;

  public function __construct()
  {
    global $conn;
    $this->conn = $conn;
  }

  //Wallet Add
  public function addWallet($user_id, $name)
  {
    $query = $this->conn->prepare("SELECT id FROM Wallets WHERE user_id = ? and name = ?");
    $query->bind_param("is", $user_id, $name);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
      return responseError("Wallet already exists");
      exit();
    }
    $query->close();
    $starting_balance = 0;
    $query = $this->conn->prepare("INSERT INTO Wallets (user_id, name, balance) VALUES (?, ?, ?)");
    $query->bind_param("isd", $user_id, $name, $starting_balance);
    $success = $query->execute();

    if ($success) {
      $walletId = $this->conn->insert_id;

      $newCard = new Card();
      $cardSuccess = $newCard->addCard($user_id, $walletId);

      if (!$cardSuccess) {
        $this->delete($walletId);
        return responseError("Failed to add Wallet - Card");
      }

      return responseSuccess(
        "Wallet added successfully",
        ["id" => $walletId]
      );
    } else {
      return responseError("Failed to add Wallet");
    }
  }

  //Get All Wallets
  public static function getAllWallets()
  {
    global $conn;
    $query = $conn->prepare("SELECT * FROM Wallets ORDER BY id DESC");
    $query->execute();
    $result = $query->get_result();
    $wallets = [];

    while ($wallet = $result->fetch_assoc()) {
      $wallets[] = $wallet;
    }

    return json_encode($wallets);
  }

  //Get Wallet BY ID
  public function getWalletById($id)
  {
    if (empty($id)) {
      return responseError("Wallet ID is required");
    }

    $query = $this->conn->prepare("SELECT * FROM Wallets WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $wallet = $result->fetch_assoc();

    if ($wallet) {
      return responseSuccess("Wallet found", $wallet);
    } else {
      return responseError("Wallet not found");
    }
  }

  // Update Wallet
  public function update($id, $user_id, $name, $balance)
  {
    if (empty($id)) {
      return responseError("Wallet ID is required");
    }

    $query = $this->conn->prepare("UPDATE Wallets SET user_id = ?, name = ?, balance = ? WHERE id = ?");

    $query->bind_param("isdi", $user_id, $name, $balance, $id);
    $success = $query->execute();

    if ($success) {
      return responseSuccess("Wallet updated successfully");
    } else {
      return responseError("Failed to update Wallet");
    }
  }

  // Delete Wallet
  public function delete($id)
  {
    if (empty($id)) {
      return responseError("Wallet ID is missing");
    }

    $query = $this->conn->prepare("DELETE FROM Wallets WHERE id = ?");
    $query->bind_param("i", $id);
    $success = $query->execute();

    if ($success) {
      $delCard = new Card();
      $delCard->deleteCardByWallet($id);
      return responseSuccess("Wallet deleted successfully");
    } else {
      return responseError("Failed to delete Wallet");
    }
  }

  // Delete deleteUser Wallets
  public function deleteAllWalletsForUser($user_id)
  {
    if (empty($user_id)) {
      return responseError("ID is missing");
    }

    $query = $this->conn->prepare("DELETE FROM Wallets WHERE user_id = ?");
    $query->bind_param("i", $user_id);
    $success = $query->execute();
    $delAllCards = new Card();
    $delAllCards->deleteAllCardsByUser($user_id);
    return $success;
  }

  // Get all Wallets of the User
  public function getAllWalletsByUserId($user_id)
  {
    if (empty($user_id)) {
      return responseError("User ID is required");
      exit();
    }
    $query = $this->conn->prepare("SELECT * FROM Wallets WHERE user_id = ? ORDER BY id");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    $wallets = [];

    while ($wallet = $result->fetch_assoc()) {
      $wallets[] = $wallet;
    }

    return responseSuccess("Wallets by user", $wallets);
  }
}
