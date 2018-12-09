<?php
include 'private/db.php';
$handleOptions = include './private/options.php';
$handleOptions();
$db = new DatabaseConnection();
$login = include './private/login_f.php';

$username = $_POST['username'];
$password = $_POST['password'];
$league = $_POST['league'];
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
echo $login($username, $password, $league, $db);
$db->close();
?>