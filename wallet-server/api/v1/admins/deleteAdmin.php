<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/adminModel.php";


if (!isset($data["id"])) {
    die(responseError("Missing admin ID"));
}

$admin = new Admin();
$response = $admin->delete($data["id"]);
echo $response;
