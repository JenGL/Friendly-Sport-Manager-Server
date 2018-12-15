<?php
include 'private/db.php';
include 'private/common_to_all.php';

$db = new DatabaseConnection();
$isAuthorized = include './private/auth.php';
$createError = include './private/errors.php';
$token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $addPlayer = include './private/addplayer.php';
        $name = $_POST['name'];
        $account = $_POST['account'];
        $league = $_POST['league'];
        $role = $_POST['role'];

        if (isset($league)) {
            if ($isAuthorized($token, $league, $db, true)) {
                echo $addPlayer($name, $account, $role, $league, $db);
            } else {
                echo $createError(403);
            }
        } else {
            echo $createError(400);
        }
        break;
    case 'GET':
        $getPlayerDetail = include './private/getPlayerDetail.php';
        if (isset($_GET['id'])) {
            $res = $db->query('SELECT Leagues.name FROM Players LEFT JOIN Leagues ON (Players.league = Leagues.id) WHERE Players.id = ' . $_GET['id'])->fetch_assoc();
            if (isset($res)) {
                $league = $res['name'];
                if ($isAuthorized($token, $league, $db)) {
                    echo $getPlayerDetail($_GET['id'], $db);
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
};


$db->close();