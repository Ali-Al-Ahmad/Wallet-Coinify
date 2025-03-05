<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/userModel.php";


if (!isset($data["id"]) || !isset($data["password"])) {
    die(responseError("Missing information!"));
}

$user = new User();
$response = $user->resetPassword($data["id"], $data["password"]);
echo $response;
