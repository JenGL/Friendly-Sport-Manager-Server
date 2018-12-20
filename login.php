<?php
include 'private/db.php';
include 'private/common_to_all.php';

$db = new DatabaseConnection();

switch($_SERVER['REQUEST_METHOD']){
    case 'POST':
        $login = include './private/login_f.php';
        $username = $_POST['username'];
        $password = $_POST['password'];
        echo $login($username, $password, $db);
        break;
    case 'GET':
        $login = include './private/login_by_token.php';
        $token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
        echo $login($token, $db);
        break;
}

$db->close();
