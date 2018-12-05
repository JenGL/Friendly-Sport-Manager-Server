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
                    $expire = time() + (4 * 60 * 60);
                    $db->query('INSERT INTO Tokens (`token`,`UUID`,`expires`) VALUES (' . $token . ',' . $res_user["UUID"] . ',' . $expire . ')');
                    $arr = array('username' => $username, 'league' => $league, 'token' => $token, 'expires' => $expire);
                    $db->commit_transaction();
                    return json_encode($arr);
                } else {
                    $db->rollback();
                    return 'ERROR: not authorized to this league';
                }
            } else {
                $db->rollback();
                return 'ERROR: wrong password';
            }
        } else {
            $db->rollback();
            return 'ERROR: user or league not valid';
        }
    }
}
?>