<?php

return function ($username, $password, $db) {
    $createError = include './errors.php';
    if (isset($username) && isset($password)) {
        $res_user = $db->query('SELECT UUID, password FROM Account WHERE username = "' . $username . '"')->fetch_assoc();
        if (isset($res_user)) {
            if ($res_user["password"] == $password) {
                $res_acc_to_league = $db->query('SELECT * FROM Acc_to_Leagues LEFT JOIN Leagues ON (Acc_to_Leagues.league = Leagues.id) WHERE Acc_to_Leagues.account =' . $res_user["UUID"]);
                $leagues = array();
                while ($row = $res_acc_to_league->fetch_assoc()) {
                    array_push($leagues, array("league" => $row['name'], "admin" => $row['admin']  == "1" ? true : false));
                }
                if (sizeof($leagues) > 0) {
                    $db->start_transaction();
                    $token = bin2hex(openssl_random_pseudo_bytes(16));
                    $expire = date('Y-m-d G:i:s', mktime(date("G") + 4, date("i"), date("s"), date("m"), date("d"), date("Y")));
                    $db->query('INSERT INTO Tokens (`token`,`UUID`,`expires`) VALUES ("' . $token . '",' . $res_user["UUID"] . ',"' . $expire . '")');
                    $arr = array('username' => $username, 'leagues' => $leagues, 'token' => $token, 'expires' => $expire);
                    if ($db->commit_transaction()) {
                        http_response_code(200);
                        return json_encode($arr);
                    } else {
                        return $createError(500);
                    }
                } else {
                    return $createError(403);
                }
            } else {
                return $createError(401);
            }
        } else {
            return $createError(401);
        }
    }
};