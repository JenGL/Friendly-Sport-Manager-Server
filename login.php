<?php
include 'private/db.php';
include 'private/common_to_all.php';

$db = new DatabaseConnection();
$login = include './private/login_f.php';
$username = $_POST['username'];
$password = $_POST['password'];
$league = $_POST['league'];
echo $login($username, $password, $league, $db);
$db->close();