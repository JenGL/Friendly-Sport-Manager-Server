<?php
return function ($league, $db) {
    $res_players_rows = $db->query(team_query($league));
    $arr = array();
    while ($row = $res_players_rows->fetch_assoc()) {
        array_push($arr, $row);
    }
    http_response_code(200);
    return json_encode($arr);
};


function team_query($league)
{
    return 'SELECT 
            P1.name as p1_name, 
            P2.name as p2_name, 
            P3.name as p3_name, 
            P4.name as p4_name,
            P5.name as p5_name,
            Teams.played, Teams.win, Teams.id
            FROM `Teams` 
            JOIN Players as P1 ON ( Teams.player1 = P1.id ) 
            JOIN Players as P2 ON ( Teams.player2 = P2.id ) 
            JOIN Players as P3 ON ( Teams.player3 = P3.id ) 
            JOIN Players as P4 ON ( Teams.player4 = P4.id ) 
            JOIN Players as P5 ON ( Teams.player5 = P5.id )
            JOIN Leagues as L ON ( P1.league = L.id )
            WHERE L.name = "' . $league . '"';
}