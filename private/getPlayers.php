<?php
return function ($league, $db) {
    $res_players_rows = $db->query('SELECT Players.* FROM Players LEFT JOIN Leagues ON (Players.league = Leagues.id) WHERE Leagues.name = "' . $league . '"');
    $arr = array();
    while($row = $res_players_rows->fetch_assoc()){
        unset($row['UUID']);
        unset($row['league']);
        array_push($arr, $row);
    }
    http_response_code(200);
    return json_encode($arr);
};



