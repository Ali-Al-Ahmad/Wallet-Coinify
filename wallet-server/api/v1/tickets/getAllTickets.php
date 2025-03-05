<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/ticketModel.php";


//check if no id get All tickets
if (!isset($data["id"])) {

  $response = Ticket::getAlltickets();
  echo $response;
  exit();
}

//get ticket by id if provided
$ticket = new Ticket();
$response = $ticket->getticketById($data["id"]);
echo $response;
