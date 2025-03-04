<?php
class Ticket
{
  private $conn;

  public function __construct()
  {
    global $conn;
    $this->conn = $conn;
  }

  //Ticket Add
  public function addTicket($user_id, $subject, $description)
  {
    $description = "pending";
    $query = $this->conn->prepare("INSERT INTO Tickets (user_id, subject, description,status) VALUES (?, ?, ?, ?)");
    $query->bind_param("isss", $user_id, $subject, $description, $description);
    $success = $query->execute();

    if ($success) {
      $ticketId = $this->conn->insert_id;

      return responseSuccess(
        "Ticket added successfully",
        ["id" => $ticketId]
      );
    } else {
      return responseError("Failed to add Ticket");
    }
  }
}
