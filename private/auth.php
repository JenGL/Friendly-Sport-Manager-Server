<?php

return function ($token, $league, $db) {
    if (isset($token) && isset($league)) {
        $token_res = $db->query('SELECT * FROM Tokens WHERE token = "' . $token . '"')->fetch_assoc();
        $today = date('Y-m-d G:i:s');
        if (isset($token_res) && strtotime($token_res['expires']) > strtotime($today)) {
            $query = 'SELECT Acc_to_Leagues.account,Leagues.name FROM Acc_to_Leagues LEFT JOIN Leagues ON (Acc_to_Leagues.league = Leagues.id) WHERE account = "' . $token_res['UUID'] . '" AND name = "' . $league . '"';
            $res_league = $db->query($query)->fetch_assoc();
            if (isset($res_league)){
                return true;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
};

