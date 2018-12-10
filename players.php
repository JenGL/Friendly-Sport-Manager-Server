<?php
include 'private/db.php';
include 'private/common_to_all.php';

$db = new DatabaseConnection();
$isAuthorized = include './private/auth.php';
$getPlayers = include './private/getPlayers.php';
$token = substr ($_SERVER['HTTP_AUTHORIZATION'] , 7);

if(isset($_GET['league'])){
    if($isAuthorized($token, $_GET['league'], $db)){
        echo $getPlayers($_GET['league'], $db);
    } else {
        http_response_code(403);
        $arr = array('error' => 'Not authorized');
        echo json_encode($arr);
    }
} else {
    http_response_code(400);
    $arr = array('error' => 'Bad Format');
    echo json_encode($arr);
}

$db->close();