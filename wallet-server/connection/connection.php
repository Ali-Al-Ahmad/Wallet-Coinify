<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type");


$db_host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "wallet";

$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$data = [];
// check if request is JSON or formdata before pass body to any api
if ($_SERVER['REQUEST_METHOD'] === "GET" || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
  $data = $_GET;
} else {
  if (isset($_SERVER["CONTENT_TYPE"]) && $_SERVER["CONTENT_TYPE"] !== "application/json") {

    $data = $_POST;
  } else {
    $data = json_decode(file_get_contents("php://input"), true) ?? [];
  }
}
