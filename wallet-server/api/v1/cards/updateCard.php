<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/cardModel.php";


if (!isset($data["id"]) || !isset($data["wallet_id"]) || !isset($data["number"]) || !isset($data["pin"]) || !isset($data["expiry_date"])) {
    die(responseError("Missin fields"));
}
$card = new Card();
$response = $card->update($data["id"], $data["wallet_id"], $data["number"], $data["pin"], $data["expiry_date"]);
echo $response;