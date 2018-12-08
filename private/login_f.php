<?php

return function($username, $password, $league, $db){
    if (isset($username) && isset($password) && isset($league)) {

        $db->start_transaction();
        $res_user = $db->query('SELECT UUID, password FROM Account WHERE username = "' . $username . '"')->fetch_assoc();
        $res_league = $db->query('SELECT id FROM Leagues WHERE `name` = "' . $league . '"')->fetch_assoc();
        if (isset($res_user) && isset($res_league)) {
            if ($res_user["password"] == $password) {
                $res_acc_to_league = $db->query('SELECT * FROM Acc_to_Leagues WHERE account = "' . $res_user["UUID"] . '" AND league = "' . $res_league["id"] . '"')->fetch_assoc();
                if (isset($res_acc_to_league)) {
                    $token = bin2hex(openssl_random_pseudo_bytes(16));
                    $expire = date('Y-m-d G:i:s',mktime(date("G") + 4, date("i"), date("s"), date("m")  , date("d"), date("Y")));
                    $db->query('INSERT INTO Tokens (`token`,`UUID`,`expires`) VALUES ("' . $token . '",' . $res_user["UUID"] . ',"' . $expire . '")');
                    $arr = array('username' => $username, 'league' => $league, 'token' => $token, 'expires' => $expire);
                    $db->commit_transaction();
                    http_response_code(200);
                    return json_encode($arr);
                } else {
                    http_response_code(403);
                    $db->rollback();
                    $arr = array('error' => 'Not authorized to this league');
                    return json_encode($arr);
                }
            } else {
                http_response_code(401);
                $db->rollback();
                $arr = array('error' => 'wrong username or password');
                return json_encode($arr);
            }
        } else {
            http_response_code(401);
            $db->rollback();
            $arr = array('error' => 'User or League not valid');
            return  json_encode($arr);
        }
    }
}
?>