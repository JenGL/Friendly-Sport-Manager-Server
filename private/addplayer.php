<?php

return function ($name, $account, $role, $league, $db) {
    $createError = include './errors.php';
    if (isset($name) && isset($league)) {
        $UUID = null;
        if (isset($account)) {
            $res_account = $db->query('SELECT UUID FROM Account WHERE `username` = "' . $account . '"')->fetch_assoc();
            if (isset($res_account)) {
                $UUID = $res_account['UUID'];
            }
        }

        $league_id = $db->query('SELECT id FROM Leagues WHERE name = "' . $league . '"')->fetch_assoc()['id'];

        $db->start_transaction();
        $db->query('INSERT INTO Players (`name`,`role`,`league`) SELECT * FROM (SELECT "' . $name . '","' . $role . '","' . $league_id . '") AS tmp
              WHERE NOT EXISTS ( SELECT id FROM Players WHERE `name` = "' . $name . '" AND `league` = "' . $league_id . '") LIMIT 1');

        $p_id = $db->query('SELECT id FROM Players WHERE `name` = "' . $name . '"')->fetch_assoc()['id'];
        if (isset($UUID)) {
            $db->query('UPDATE Players SET UUID = ' . $UUID . ' WHERE id = ' . $p_id);
        }

        if ($db->commit_transaction()) {
            http_response_code(201);
            return;
        } else {
            return $createError(500);
        }
    } else {
        return $createError(400);
    }
};
