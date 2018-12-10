<?php

return function ($token, $league, $db, $admin = false) {
    if (isset($token) && isset($league)) {
        $token_res = $db->query('SELECT * FROM Tokens WHERE token = "' . $token . '"')->fetch_assoc();
        $today = date('Y-m-d G:i:s');
        if (isset($token_res) && strtotime($token_res['expires']) > strtotime($today)) {
            $query = 'SELECT * FROM Acc_to_Leagues LEFT JOIN Leagues ON (Acc_to_Leagues.league = Leagues.id) WHERE account = "' . $token_res['UUID'] . '" AND name = "' . $league . '"';
            if ($admin) {
                $query .= " AND admin = true";
            }
            $res_league = $db->query($query)->fetch_assoc();
            if (isset($res_league)) {
                return true;
            }
        }
    }
    return false;
};

