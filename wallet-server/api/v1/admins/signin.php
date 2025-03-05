<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/adminModel.php";


if (!isset($data["email"]) || !isset($data["password"])) {
  die(responseError("Missing email or password!"));
}

$admin = new Admin();
$response = $admin->signIn($data["email"], $data["password"]);
echo $response;
