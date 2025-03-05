<?php
require_once "../../../connection/connection.php";
require_once "../../../utils/utils.php";
require_once "../../../models/cardModel.php";


//check if no id get All cards
if (!isset($data["id"])) {

  $response = Card::getAllcards();
  echo $response;
  exit();
}

//get card by id if provided
$card = new Card();
$response = $card->getcardById($data["id"]);
echo $response;
