<?php
include 'private/db.php';
include 'private/common_to_all.php';

$db = new DatabaseConnection();
$register = include './private/register_f.php';

$username = $_POST['username'];
$password = $_POST['password'];
$league = $_POST['league'];

echo $register($username, $password, $league, $db);
$db->close();
?>