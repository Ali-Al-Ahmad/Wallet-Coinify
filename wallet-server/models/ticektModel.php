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

  //Get All Tickets
  public static function getAllTickets()
  {
    global $conn;
    $query = $conn->prepare("SELECT * FROM Tickets ORDER BY id DESC");
    $query->execute();
    $result = $query->get_result();
    $tickets = [];

    while ($ticket = $result->fetch_assoc()) {
      $tickets[] = $ticket;
    }

    return json_encode($tickets);
  }

  //Get Ticket BY ID
  public function getTicketById($id)
  {
    if (empty($id)) {
      return responseError("Ticket ID is required");
    }

    $query = $this->conn->prepare("SELECT * FROM Tickets WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $ticket = $result->fetch_assoc();

    if ($ticket) {
      return responseSuccess("Ticket found", $ticket);
    } else {
      return responseError("Ticket not found");
    }
  }

  // Update Ticket
  public function update($id, $user_id, $subject, $description, $status)
  {
    if (empty($id)) {
      return responseError("Ticket ID is required");
    }

    $query = $this->conn->prepare("UPDATE Tickets SET user_id = ?, subject = ?, description = ?,status = ? WHERE id = ?");

    $query->bind_param("isssi", $user_id, $subject, $description, $status, $id);
    $success = $query->execute();

    if ($success) {
      return responseSuccess("Ticket updated successfully");
    } else {
      return responseError("Failed to update Ticket");
    }
  }

  // Delete Ticket
  public function delete($id)
  {
    if (empty($id)) {
      return responseError("Ticket ID is missing");
    }

    $query = $this->conn->prepare("DELETE FROM Tickets WHERE id = ?");
    $query->bind_param("i", $id);
    $success = $query->execute();

    if ($success) {
      return responseSuccess("Ticket deleted successfully");
    } else {
      return responseError("Failed to delete Ticket");
    }
  }
}
