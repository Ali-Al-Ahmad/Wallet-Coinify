<?php
class Admin
{
  private $conn;

  public function __construct()
  {
    global $conn;
    $this->conn = $conn;
  }

  //Admin Singup
  public function signUp($email, $password)
  {
    $query = $this->conn->prepare("SELECT id FROM Admins WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
      return responseError("Admin already exists");
    }
    $query->close();

    $hashedPassword = hashPassword($password);

    $query = $this->conn->prepare("INSERT INTO Admins (email, password) VALUES (?, ?)");
    $query->bind_param("ss", $email, $hashedPassword);
    $success = $query->execute();

    if ($success) {
      $AdminId = $this->conn->insert_id;
      return responseSuccess(
        "Admin added successfully",
        ["id" => $AdminId, "email" => $email]
      );
    } else {
      return responseError("Failed to signup Admin");
    }
  }

  //Admin Singin
  public function signIn($email, $password)
  {
    if (empty($email) || empty($password)) {
      return responseError("Missing field is required.");
    }

    $query = $this->conn->prepare("SELECT id, password FROM Admins WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    $Admin = $result->fetch_assoc();

    if ($Admin && verifyPassword($password, $Admin["password"])) {
      return responseSuccess("Sign in successful", ["id" => $Admin["id"], "email" => $email]);
    } else {
      return responseError("Wrong Email or Password");
    }
  }

  //Admin Reset Password
  public function resetPassword($id, $newPassword)
  {
    if (empty($id) || empty($newPassword)) {
      return responseError("Missing field is required.");
    }

    $query = $this->conn->prepare("SELECT id FROM Admins WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 0) {
      return responseError("No Admin found with this id");
    }

    $hashedPassword = hashPassword($newPassword);

    $query = $this->conn->prepare("UPDATE Admins SET password = ? WHERE id = ?");
    $query->bind_param("si", $hashedPassword, $id);
    $success = $query->execute();

    if ($success) {
      return responseSuccess("Password reset successfully.");
    } else {
      return responseError("Failed to reset password.");
    }
  }

  //Get All Admins
  public static function getAllAdmins()
  {
    global $conn;
    $query = $conn->prepare("SELECT * FROM Admins ORDER BY id DESC");
    $query->execute();
    $result = $query->get_result();
    $Admins = [];

    while ($Admin = $result->fetch_assoc()) {
      $Admins[] = $Admin;
    }

    return json_encode($Admins);
  }
}
