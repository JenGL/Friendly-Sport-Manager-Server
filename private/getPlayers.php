<?php
return function ($league, $db) {
    $res_players_rows = $db->query(query_players($league));
    $arr = array();
    while ($row = $res_players_rows->fetch_assoc()) {
        array_push($arr, $row);
    }
    http_response_code(200);
    return json_encode($arr);
};


function query_players($league)
{
    return 'SELECT Players.id, Players.name,Players.role,Players.points,Players.goal,Players.autogoal 
            FROM `Players` LEFT JOIN Leagues ON (Players.league = Leagues.id) WHERE Leagues.name = "' . $league . '"';
}