<?php
return function ($token, $db) {
    $createError = include './errors.php';
    if (isset($token)) {
        $res_token = $db->query('SELECT * FROM Tokens WHERE token = "' . $token . '"')->fetch_assoc();
        $today = date('Y-m-d G:i:s');
        if (isset($res_token) && strtotime($res_token['expires']) > strtotime($today)) {
            $res_user = $db->query('SELECT * FROM Account WHERE UUID = "' . $res_token["UUID"] . '"')->fetch_assoc();
            if (isset($res_user)) {
                $res_acc_to_league = $db->query('SELECT * FROM Acc_to_Leagues LEFT JOIN Leagues ON (Acc_to_Leagues.league = Leagues.id) WHERE Acc_to_Leagues.account =' . $res_user["UUID"]);
                $leagues = array();
                while ($row = $res_acc_to_league->fetch_assoc()) {
                    array_push($leagues, array("league" => $row['name'], "admin" => $row['admin'] == "1" ? true : false));
                }
                if (sizeof($leagues) > 0) {
                    $arr = array('username' => $res_user['username'], 'leagues' => $leagues);
                    http_response_code(200);
                    return json_encode($arr);
                }
            }
        }
        return $createError(403);
    } else {
        return $createError(400);
    }
};