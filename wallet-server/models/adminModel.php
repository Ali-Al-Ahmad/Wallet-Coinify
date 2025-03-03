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
}
