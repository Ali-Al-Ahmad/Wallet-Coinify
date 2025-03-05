<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/userModel.php";


//check if no id get All Users
if (!isset($data["id"])) {

  $response = User::getAllUsers();
  echo $response;
  exit();
}

//get user by id if provided
$user = new User();
$response = $user->getUserById($data["id"]);
echo $response;
