<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/adminModel.php";


//check if no id get All admins
if (!isset($data["id"])) {

  $response = Admin::getAlladmins();
  echo $response;
  exit();
}

//get admin by id if provided
$admin = new Admin();
$response = $admin->getadminById($data["id"]);
echo $response;
