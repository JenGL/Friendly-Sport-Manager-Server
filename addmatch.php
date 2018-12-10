<?php
include 'private/db.php';
include 'private/common_to_all.php';

$db = new DatabaseConnection();
$isAuthorized = include './private/auth.php';
$addMatch = include './private/addmatch.php';

$team1 = json_decode($_POST['team1']);
$team2 = json_decode($_POST['team2']);
$data = $_POST['data'];
$league = $_POST['league'];
$token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);

if (isset($league)) {
    if ($isAuthorized($token, $league, $db, true)) {
        echo $addMatch($team1, $team2, $league, $data, $db);
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
