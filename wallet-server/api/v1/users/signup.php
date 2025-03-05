<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/userModel.php";


if (!isset($data["email"]) || !isset($data["password"]) || !isset($data["phone"]) || !isset($data["first_name"]) || !isset($data["last_name"])) {

  die(responseError("Missing Fields!"));
}

$user = new User();
$response = $user->signUp($data["email"], $data["password"], $data["phone"], $data["first_name"], $data["last_name"]);

echo $response;
