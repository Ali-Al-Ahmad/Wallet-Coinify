<?php
//return response with success message
function responseSuccess($message, $data = null)
{
  $response = ["status" => "success", "message" => $message, "data" => $data];
  return json_encode($response);
}

//return response with error message
function responseError($message)
{
  $response = ["status" => "error", "message" => $message];
  return json_encode($response);
}
