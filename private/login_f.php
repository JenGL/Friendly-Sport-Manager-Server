<?php

return function($username, $password, $league, $db){
    $createError = include './errors.php';
    if (isset($username) && isset($password) && isset($league)) {
        $res_user = $db->query('SELECT UUID, password FROM Account WHERE username = "' . $username . '"')->fetch_assoc();
        $res_league = $db->query('SELECT id FROM Leagues WHERE `name` = "' . $league . '"')->fetch_assoc();
        if (isset($res_user) && isset($res_league)) {
            if ($res_user["password"] == $password) {
                $res_acc_to_league = $db->query('SELECT * FROM Acc_to_Leagues WHERE account = "' . $res_user["UUID"] . '" AND league = "' . $res_league["id"] . '"')->fetch_assoc();
                if (isset($res_acc_to_league)) {
                    $db->start_transaction();
                    $token = bin2hex(openssl_random_pseudo_bytes(16));
                    $expire = date('Y-m-d G:i:s',mktime(date("G") + 4, date("i"), date("s"), date("m")  , date("d"), date("Y")));
                    $db->query('INSERT INTO Tokens (`token`,`UUID`,`expires`) VALUES ("' . $token . '",' . $res_user["UUID"] . ',"' . $expire . '")');
                    $arr = array('username' => $username, 'league' => $league, 'token' => $token, 'expires' => $expire, 'admin' => $res_acc_to_league['admin'] == "1" ? true : false);
                    if($db->commit_transaction()){
                        http_response_code(200);
                        return json_encode($arr);
                    } else {
                        return  $createError(500);
                    }
                } else {
                    return  $createError(403);
                }
            } else {
                return  $createError(401);
            }
        } else {
            return  $createError(401);
        }
    }
};