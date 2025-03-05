<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/userModel.php";


if (!isset($data["id"])) {
    die(responseError("Missing user ID"));
}

$user = new User();
$response = $user->delete($data["id"]);
echo $response;
