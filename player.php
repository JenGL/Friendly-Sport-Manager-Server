<?php
include 'private/db.php';
$handleOptions = include './private/options.php';
$handleOptions();
$db = new DatabaseConnection();

$isAuthorized = include './private/auth.php';
$getPlayerDetail = include './private/getPlayerDetail.php';
$token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
if(isset($_GET['id'])){
    $res = $db->query('SELECT Leagues.name FROM Players LEFT JOIN Leagues ON (Players.league = Leagues.id) WHERE Players.id = ' . $_GET['id'])->fetch_assoc();
    if(isset($res)) {
        $league = $res['name'];
        if ($isAuthorized($token, $league, $db)) {
            echo $getPlayerDetail($_GET['id'], $db);
        } else {
            http_response_code(403);
            $arr = array('error' => 'Not authorized');
            echo json_encode($arr);
        }
    } else {
        http_response_code(404);
        $arr = array('error' => 'Match Not Found');
        echo json_encode($arr);
    }
} else {
    http_response_code(400);
    $arr = array('error' => 'Bad format');
    echo json_encode($arr);
}


$db->close();