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

    return 'SELECT Players.id, Players.name,Players.role,Players.points,Players.goal,Players.autogoal, 
              (SELECT COUNT(*) FROM Matches LEFT JOIN Teams AS t1 ON (Matches.team1 = t1.id) LEFT JOIN Teams AS t2 ON (Matches.team2 = t2.id) 
              WHERE t1.player1 = Players.id 
                 OR t1.player1 = Players.id 
                 OR t1.player2 = Players.id 
                 OR t1.player3 = Players.id 
                 OR t1.player4 = Players.id 
                 OR t1.player5 = Players.id
                 OR t2.player1 = Players.id
                 OR t2.player2 = Players.id 
                 OR t2.player3 = Players.id 
                 OR t2.player4 = Players.id 
                 OR t2.player5 = Players.id) AS played 
            FROM `Players` LEFT JOIN Leagues ON (Players.league = Leagues.id) WHERE Leagues.name = "' . $league . '"';
}