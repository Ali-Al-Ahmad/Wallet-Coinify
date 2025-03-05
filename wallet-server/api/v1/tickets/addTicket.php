<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/ticketModel.php";


if (!isset($data["user_id"]) || !isset($data["subject"]) || !isset($data["description"])) {
    die(responseError("Missing Fields!"));
}

$ticket = new Ticket();
$response = $ticket->addticket($data["user_id"], $data["subject"], $data["description"]);

echo $response;
