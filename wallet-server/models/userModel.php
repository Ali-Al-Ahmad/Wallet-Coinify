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

  //User Singin
  public function signIn($email, $password)
  {
    if (empty($email) || empty($password)) {
      return responseError("Missing field is required.");
    }

    $query = $this->conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    $user = $result->fetch_assoc();

    if ($user && verifyPassword($password, $user["password"])) {
      return responseSuccess("Sign in successful", ["id" => $user["id"], "email" => $email]);
    } else {
      return responseError("Wrong Email or Password");
    }
  }

  //User Reset Password
  public function resetPassword($id, $newPassword)
  {
    if (empty($id) || empty($newPassword)) {
      return responseError("Missing field is required.");
    }
    $query = $this->conn->prepare("SELECT id FROM users WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 0) {
      return responseError("No user found with this id");
    }

    $hashedPassword = hashPassword($newPassword);

    $query = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $query->bind_param("si", $hashedPassword, $id);
    $success = $query->execute();

    if ($success) {
      return responseSuccess("Password reset successfully.");
    } else {
      return responseError("Failed to reset password.");
    }
  }

  //Get All Users
  public static function getAllUsers()
  {
    global $conn;
    $query = $conn->prepare("SELECT * FROM users ORDER BY id DESC");
    $query->execute();
    $result = $query->get_result();
    $users = [];

    while ($user = $result->fetch_assoc()) {
      $users[] = $user;
    }

    return json_encode($users);
  }
}
