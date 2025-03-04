<?php
require_once("walletModel.php");

class User
{
  private $conn;

  public function __construct()
  {
    global $conn;
    $this->conn = $conn;
  }

  //User Singup
  public function signUp($email, $password, $phone, $first_name, $last_name)
  {
    $query = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
      return responseError("User already exists");
    }

    $query = $this->conn->prepare("SELECT id FROM users WHERE phone = ?");
    $query->bind_param("s", $phone);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
      return responseError("Phone already exists");
    }

    $hashedPassword = hashPassword($password);

    $query = $this->conn->prepare("INSERT INTO users (email, password,phone,first_name,last_name) VALUES (?,?,?,?,?)");
    $query->bind_param("sssss", $email, $hashedPassword, $phone, $first_name, $last_name);
    $success = $query->execute();

    if ($success) {
      $userId = $this->conn->insert_id;
      return responseSuccess(
        "User added successfully",
        ["id" => $userId, "email" => $email]
      );
    } else {
      return responseError("Failed to signup user");
    }
  }
}
