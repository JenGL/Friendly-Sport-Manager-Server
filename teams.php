<?php
include 'private/db.php';
include 'private/common_to_all.php';

$db = new DatabaseConnection();
$isAuthorized = include './private/auth.php';
$getTeams = include './private/getTeams.php';
$createError = include './private/errors.php';
$token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);

if (isset($_GET['league'])) {
    if ($isAuthorized($token, $_GET['league'], $db)) {
        echo $getTeams($_GET['league'], $db);
    } else {
        echo $createError(403);
    }
} else {
    echo $createError(400);
}

$db->close();