<?php
include 'private/db.php';
include 'private/common_to_all.php';

$db = new DatabaseConnection();
$login = include './private/login_f.php';
$username = $_POST['username'];
$password = $_POST['password'];
echo $login($username, $password, $db);
$db->close();