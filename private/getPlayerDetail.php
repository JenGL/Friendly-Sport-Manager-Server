<?php
return function ($id, $db) {
    $base_info = $db->query('SELECT * FROM Players WHERE id = '.$id)->fetch_assoc();

    $q = buildQuery($id);
    $res_matches_rows = $db->query($q);
    $arr = array();
    while ($row = $res_matches_rows->fetch_assoc()) {
        array_push($arr, $row);
    }
    http_response_code(200);
    return json_encode($arr);
};

function getPlayerObj($row, $p){
    return array("name" => $row[$p.'_name'],"goal" => $row[$p.'_goal'],"autogoal" => $row[$p.'_autogoal']);
}

function buildQuery($id)
{
    return 'SELECT Matches.id, Matches.Data,
            CASE 
            WHEN g1.player='.$id.' THEN IFNULL(g1.goal, 0)
            WHEN g2.player='.$id.' THEN IFNULL(g2.goal, 0)
            WHEN g3.player='.$id.' THEN IFNULL(g3.goal, 0)
            WHEN g4.player='.$id.' THEN IFNULL(g4.goal, 0)
            WHEN g5.player='.$id.' THEN IFNULL(g5.goal, 0)
            WHEN f1.player='.$id.' THEN IFNULL(f1.goal, 0)
            WHEN f2.player='.$id.' THEN IFNULL(f2.goal, 0)
            WHEN f3.player='.$id.' THEN IFNULL(f3.goal, 0)
            WHEN f4.player='.$id.' THEN IFNULL(f4.goal, 0)
            WHEN f5.player='.$id.' THEN IFNULL(f5.goal, 0)
            END as goal,
            
            CASE 
            WHEN g1.player='.$id.' THEN IFNULL(g1.autogoal, 0)
            WHEN g2.player='.$id.' THEN IFNULL(g2.autogoal, 0)
            WHEN g3.player='.$id.' THEN IFNULL(g3.autogoal, 0)
            WHEN g4.player='.$id.' THEN IFNULL(g4.autogoal, 0)
            WHEN g5.player='.$id.' THEN IFNULL(g5.autogoal, 0)
            WHEN f1.player='.$id.' THEN IFNULL(f1.autogoal, 0)
            WHEN f2.player='.$id.' THEN IFNULL(f2.autogoal, 0)
            WHEN f3.player='.$id.' THEN IFNULL(f3.autogoal, 0)
            WHEN f4.player='.$id.' THEN IFNULL(f4.autogoal, 0)
            WHEN f5.player='.$id.' THEN IFNULL(f5.autogoal, 0)
            END as autogoal,
            
            CASE
            WHEN IFNULL(g1.goal,0) + IFNULL(g2.goal,0) + IFNULL(g3.goal,0) + IFNULL(g4.goal,0) + IFNULL(g5.goal,0) +
            IFNULL(f1.autogoal,0) + IFNULL(f2.autogoal,0) + IFNULL(f3.autogoal,0) + IFNULL(f4.autogoal,0) + IFNULL(f5.autogoal,0) = 
            IFNULL(f1.goal,0) + IFNULL(f2.goal,0) + IFNULL(f3.goal,0) + IFNULL(f4.goal,0) + IFNULL(f5.goal,0) +
            IFNULL(g1.autogoal,0) + IFNULL(g2.autogoal,0) + IFNULL(g3.autogoal,0) + IFNULL(g4.autogoal,0) + IFNULL(g5.autogoal,0) 
            THEN 1

            WHEN (g1.player = '.$id.' OR g2.player = '.$id.' OR g3.player = '.$id.' OR g4.player = '.$id.' OR g5.player = '.$id.')
            AND (IFNULL(g1.goal,0) + IFNULL(g2.goal,0) + IFNULL(g3.goal,0) + IFNULL(g4.goal,0) + IFNULL(g5.goal,0) +
            IFNULL(f1.autogoal,0) + IFNULL(f2.autogoal,0) + IFNULL(f3.autogoal,0) + IFNULL(f4.autogoal,0) + IFNULL(f5.autogoal,0) > 
            IFNULL(f1.goal,0) + IFNULL(f2.goal,0) + IFNULL(f3.goal,0) + IFNULL(f4.goal,0) + IFNULL(f5.goal,0) +
            IFNULL(g1.autogoal,0) + IFNULL(g2.autogoal,0) + IFNULL(g3.autogoal,0) + IFNULL(g4.autogoal,0) + IFNULL(g5.autogoal,0))
            THEN 3

            WHEN (f1.player = '.$id.' OR f2.player = '.$id.' OR f3.player = '.$id.' OR f4.player = '.$id.' OR f5.player = '.$id.')
            AND (IFNULL(g1.goal,0) + IFNULL(g2.goal,0) + IFNULL(g3.goal,0) + IFNULL(g4.goal,0) + IFNULL(g5.goal,0) +
            IFNULL(f1.autogoal,0) + IFNULL(f2.autogoal,0) + IFNULL(f3.autogoal,0) + IFNULL(f4.autogoal,0) + IFNULL(f5.autogoal,0) < 
            IFNULL(f1.goal,0) + IFNULL(f2.goal,0) + IFNULL(f3.goal,0) + IFNULL(f4.goal,0) + IFNULL(f5.goal,0) +
            IFNULL(g1.autogoal,0) + IFNULL(g2.autogoal,0) + IFNULL(g3.autogoal,0) + IFNULL(g4.autogoal,0) + IFNULL(g5.autogoal,0))
            THEN 3
            
            ELSE 0 END as points
            
            FROM Matches
            LEFT JOIN Teams   AS t1 ON (Matches.team1 = t1.id) 
            LEFT JOIN Teams   AS t2 ON (Matches.team2 = t2.id)
            LEFT JOIN Players AS a1 ON (t1.player1 = a1.id)
            LEFT JOIN Players AS a2 ON (t1.player2 = a2.id)
            LEFT JOIN Players AS a3 ON (t1.player3 = a3.id)
            LEFT JOIN Players AS a4 ON (t1.player4 = a4.id)
            LEFT JOIN Players AS a5 ON (t1.player5 = a5.id)
            LEFT JOIN Players AS b1 ON (t2.player1 = b1.id)
            LEFT JOIN Players AS b2 ON (t2.player2 = b2.id)
            LEFT JOIN Players AS b3 ON (t2.player3 = b3.id)
            LEFT JOIN Players AS b4 ON (t2.player4 = b4.id)
            LEFT JOIN Players AS b5 ON (t2.player5 = b5.id)
            LEFT JOIN Goals   AS g1 ON (a1.id = g1.player) AND (Matches.id = g1.match)
            LEFT JOIN Goals   AS g2 ON (a2.id = g2.player) AND (Matches.id = g2.match)
            LEFT JOIN Goals   AS g3 ON (a3.id = g3.player) AND (Matches.id = g3.match)
            LEFT JOIN Goals   AS g4 ON (a4.id = g4.player) AND (Matches.id = g4.match)
            LEFT JOIN Goals   AS g5 ON (a5.id = g5.player) AND (Matches.id = g5.match)
            LEFT JOIN Goals   AS f1 ON (b1.id = f1.player) AND (Matches.id = f1.match)
            LEFT JOIN Goals   AS f2 ON (b2.id = f2.player) AND (Matches.id = f2.match)
            LEFT JOIN Goals   AS f3 ON (b3.id = f3.player) AND (Matches.id = f3.match)
            LEFT JOIN Goals   AS f4 ON (b4.id = f4.player) AND (Matches.id = f4.match)
            LEFT JOIN Goals   AS f5 ON (b5.id = f5.player) AND (Matches.id = f5.match)';
}


/*
{
"id" : 1,
"Name" : "Nicola Barguino",
"Role" : "Attaccante",
"Points" : 6,
"Goals" : 3,
"autogoals:0,
"Matches" : [
{
"data" : 1541548800,
"goals" : 2,
"points" : 3,
"id" : 1
},
{
"data" : 1542153600,
"goals" : 1,
"points" : 3,
"id" : 2
},
{
"data" : 1542758400,
"goals" : 0,
"points" : 0,
"id" : 3
}
]
}





*/