<?php

return function ($team1, $team2, $league, $data, $db) {
    $createError = include './errors.php';
    if (isset($team1) && isset($team2) && isset($league) && isset($data)) {
        usort($team1, "cmp");
        usort($team2, "cmp");

        $db->start_transaction();

        $league_id = $db->query('SELECT id FROM Leagues WHERE name = "' . $league . '"')->fetch_assoc()['id'];

        $team1_score = 0;
        $team2_score = 0;

        for ($i = 0; $i < 5; $i++) {
            $team1_score += $team1[$i]->goal + $team2[$i]->autogoal;
            $team2_score += $team2[$i]->goal + $team1[$i]->autogoal;
        }

        $team1_id = addTeam($team1, $league_id, $team1_score > $team2_score, $db);
        $team2_id = addTeam($team2, $league_id, $team2_score > $team1_score, $db);
        $match_id = addMatch($team1_id, $team2_id, $data, $league_id, $db);

        for ($i = 0; $i < 5; $i++) {
            addGoalForPlayer($match_id, $team1[$i], $team1_score - $team2_score, $db);
            addGoalForPlayer($match_id, $team2[$i], $team2_score - $team1_score, $db);
        }

        if ($db->commit_transaction()) {
            http_response_code(201);
            return "";
        } else {
            return $createError(500);
        }
    } else {
        return $createError(400);
    }
};

function cmp($a, $b)
{
    return $a->id - $b->id;
}

function addTeam($team, $league, $win, $db)
{
    $condition = 'player1 = ' . $team[0]->id . ' AND player2 = ' . $team[1]->id . ' AND player3 = ' . $team[2]->id . ' AND player4 = ' . $team[3]->id . ' AND player5 = ' . $team[4]->id . ' AND league = ' . $league;
    $query = 'INSERT INTO Teams (player1, player2, player3, player4, player5, league) 
              SELECT * FROM (SELECT ' . $team[0]->id . ', ' . $team[1]->id . ', ' . $team[2]->id . ', ' . $team[3]->id . ', ' . $team[4]->id . ', ' . $league . ') AS tmp  
              WHERE NOT EXISTS ( SELECT id FROM Teams WHERE ' . $condition . ') LIMIT 1';
    $db->query($query);

    $win = $win ? 1 : 0;
    $db->query('UPDATE Teams SET played = played + 1, win = win + ' . $win . ' WHERE ' . $condition);

    return $db->query('SELECT id FROM Teams WHERE ' . $condition)->fetch_assoc()['id'];
}

function addMatch($team1_id, $team2_id, $data, $league, $db)
{
    $db->query('INSERT INTO Matches (`team1`, `team2`, `data`, `league`) VALUES (' . $team1_id . ',' . $team2_id . ',"' . $data . '",' . $league . ')');
    return $db->lastInsertId();
}

function addGoalForPlayer($match, $player, $result, $db)
{
    if ($player->goal + $player->autogoal > 0) {
        $db->query('INSERT INTO Goals (`player`, `match`, `goal`, `autogoal`) VALUES (' . $player->id . ',' . $match . ',' . $player->goal . ',' . $player->autogoal . ')');
    }

    if ($result < 0) {
        $db->query('UPDATE Players SET goal = goal + ' . $player->goal . ', autogoal = autogoal + ' . $player->autogoal . ' WHERE id = ' . $player->id);
    } else if ($result > 0) {
        $db->query('UPDATE Players SET goal = goal + ' . $player->goal . ', autogoal = autogoal + ' . $player->autogoal . ', points = points + 3 WHERE id = ' . $player->id);
    } else {
        $db->query('UPDATE Players SET goal = goal + ' . $player->goal . ', autogoal = autogoal + ' . $player->autogoal . ', points = points + 1 WHERE id = ' . $player->id);
    }

    return $db->lastInsertId();
}
