<?php

return function ($username, $password, $league, $db) {
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
            http_response_code(500);
            $arr = array('error' => 'Something Went Really Wrong', 'data' => $username.' - '.$password.' - '.$league);
            return json_encode($arr);
        }
    } else {
        http_response_code(400);
        $arr = array('error' => 'Bad Format');
        return json_encode($arr);
    }
}
?>