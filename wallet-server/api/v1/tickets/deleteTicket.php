<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/ticketModel.php";


if (!isset($data["id"])) {
    die(responseError("Missing ticket ID"));
}

$ticket = new Ticket();
$response = $ticket->delete($data["id"]);
echo $response;
