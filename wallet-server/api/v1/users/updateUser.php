<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/userModel.php";


if (!isset($data["id"]) || !isset($data["email"])) {
    die(responseError("Missin fields"));
}

$user = new User();
$response = $user->update($data["id"], $data["email"]);
echo $response;
