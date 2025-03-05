<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/ticketModel.php";


if (!isset($data["id"]) || !isset($data["user_id"]) || !isset($data["subject"]) || !isset($data["description"]) || !isset($data["description"])) {
    die(responseError("Missin fields"));
}

$ticket = new Ticket();
$response = $ticket->update($data["id"], $data["user_id"], $data["subject"], $data["description"], $data["status"]);
echo $response;
