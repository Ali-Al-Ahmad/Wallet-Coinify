<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/adminModel.php";


if (!isset($data["id"]) || !isset($data["email"])) {
    die(responseError("Missin fields"));
}

$admin = new Admin();
$response = $admin->update($data["id"], $data["email"]);
echo $response;
