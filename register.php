<?php
include 'private/db.php';
$handleOptions = include './private/options.php';
$handleOptions();
$db = new DatabaseConnection();
$register = include './private/register_f.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$username = $_POST['username'];
$password = $_POST['password'];
$league = $_POST['league'];

echo $register($username, $password, $league, $db);
$db->close();
?>