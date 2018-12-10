<?php
include 'private/db.php';
include 'private/common_to_all.php';

$db = new DatabaseConnection();
$addPlayer = include './private/addplayer.php';
$isAuthorized = include './private/auth.php';

$name = $_POST['name'];
$account = $_POST['account'];
$league = $_POST['league'];
$role = $_POST['role'];
$token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);

if (isset($league)) {
    if ($isAuthorized($token, $league, $db, true)) {
        echo $addPlayer($name, $account, $role, $league, $db);
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
