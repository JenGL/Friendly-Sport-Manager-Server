<?php
include 'private/db.php';
include 'private/common_to_all.php';

$db = new DatabaseConnection();
$isAuthorized = include './private/auth.php';
$createError = include './private/errors.php';
$token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);

switch($_SERVER['REQUEST_METHOD']){
    case 'POST':
        $addMatch = include './private/addmatch.php';
        $team1 = json_decode($_POST['team1']);
        $team2 = json_decode($_POST['team2']);
        $data = $_POST['data'];
        $league = $_POST['league'];

        if (isset($league)) {
            if ($isAuthorized($token, $league, $db, true)) {
                echo $addMatch($team1, $team2, $league, $data, $db);
            } else {
                echo $createError(403);
            }
        } else {
            echo $createError(400);
        }
        break;
    case 'GET':
        $getMatchDetail = include './private/getMatchDetail.php';
        if(isset($_GET['id'])){
            $res = $db->query('SELECT Leagues.name FROM Matches LEFT JOIN Leagues ON (Matches.league = Leagues.id) WHERE Matches.id = ' . $_GET['id'])->fetch_assoc();
            if(isset($res)) {
                $league = $res['name'];
                if ($isAuthorized($token, $league, $db)) {
                    echo $getMatchDetail($_GET['id'], $db);
                } else {
                    echo $createError(403);
                }
            } else {
                echo $createError(404);
            }
        } else {
            echo $createError(400);
        }
        break;
}

$db->close();
