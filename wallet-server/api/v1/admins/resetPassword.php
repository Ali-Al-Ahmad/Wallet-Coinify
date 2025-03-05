<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/adminModel.php";


if (!isset($data["id"]) || !isset($data["password"])) {
    die(responseError("Missing information!"));
}

$admin = new Admin();
$response = $admin->resetPassword($data["id"], $data["password"]);
echo $response;
