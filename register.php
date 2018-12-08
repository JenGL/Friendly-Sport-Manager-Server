<?php
include 'private/db.php';
$db = new DatabaseConnection();
$register = include './private/register_f.php';

$username = $_POST['username'];
$password = $_POST['password'];
$league = $_POST['league'];
header("Content-Type: application/json");
echo $register($username, $password, $league, $db);
$db->close();
?>