<?php

return function ($username, $password, $league, $db) {
    $createError = include './errors.php';
    $login = include 'login_f.php';
    if (isset($username) && isset($password) && isset($league)) {
        $db->start_transaction();
        $res_league = $db->query('SELECT id FROM Leagues WHERE name = "' . $league . '"')->fetch_assoc();
        if (!isset($res_league)) {
            $db->query('INSERT INTO Leagues (`name`) VALUES ("' . $league . '")');
            $res_league = $res_league = $db->query('SELECT id FROM Leagues WHERE name = "' . $league . '"')->fetch_assoc();
        }
        $db->query('INSERT INTO Account(`username`,`password`) VALUES ("' . $username . '","' . $password . '")');
        $res_user = $db->query('SELECT UUID FROM Account WHERE username = "' . $username . '"')->fetch_assoc();
        $db->query('INSERT INTO Acc_to_Leagues(`account`,`league`) VALUES ("' . $res_user["UUID"] . '","' . $res_league['id'] . '")');
        if ($db->commit_transaction()) {
            return $login($username, $password, $league, $db);
        } else {
            return $createError(500);
        }
    } else {
        return $createError(400);
    }
};