<?php
include 'private/db.php';
$db = new DatabaseConnection();
$isAuthorized = include './private/auth.php';
$getPlayers = include './private/getPlayers.php';
$token = substr ($_SERVER['HTTP_AUTHORIZATION'] , 7);

header("Content-Type: application/json");
if($isAuthorized($token, $_GET['league'], $db)){
    echo $getPlayers($_GET['league'], $db);
} else {
    http_response_code(403);
    $arr = array('error' => 'Not authorized');
    return json_encode($arr);
}
$db->close();