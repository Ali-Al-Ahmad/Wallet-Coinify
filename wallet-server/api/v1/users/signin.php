<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/userModel.php";


if (!isset($data["email"]) || !isset($data["password"])) {
  die(responseError("Missing email or password!"));
}

$user = new User();
$response = $user->signIn($data["email"], $data["password"]);
echo $response;
