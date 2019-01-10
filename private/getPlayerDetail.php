<?php
return function ($id, $db) {
    $base_info = $db->query('SELECT * FROM Players WHERE id = '.$id)->fetch_assoc();
    unset($base_info['UUID']);
    $q = buildQuery($id);
    $res_matches_rows = $db->query($q);
    $base_info['matches'] = array();
    while ($row = $res_matches_rows->fetch_assoc()) {
        array_push($base_info['matches'], $row);
    }
    http_response_code(200);
    return json_encode($base_info);
};

function buildQuery($id)
{
    return 'SELECT Matches.id, Matches.data,
            CASE 
            WHEN g1.player='.$id.' THEN g1.goal
            WHEN g2.player='.$id.' THEN g2.goal
            WHEN g3.player='.$id.' THEN g3.goal
            WHEN g4.player='.$id.' THEN g4.goal
            WHEN g5.player='.$id.' THEN g5.goal
            WHEN f1.player='.$id.' THEN f1.goal
            WHEN f2.player='.$id.' THEN f2.goal
            WHEN f3.player='.$id.' THEN f3.goal
            WHEN f4.player='.$id.' THEN f4.goal
            WHEN f5.player='.$id.' THEN f5.goal
            ELSE 0
            END as goal,
            
            CASE 
            WHEN g1.player='.$id.' THEN g1.autogoal
            WHEN g2.player='.$id.' THEN g2.autogoal
            WHEN g3.player='.$id.' THEN g3.autogoal
            WHEN g4.player='.$id.' THEN g4.autogoal
            WHEN g5.player='.$id.' THEN g5.autogoal
            WHEN f1.player='.$id.' THEN f1.autogoal
            WHEN f2.player='.$id.' THEN f2.autogoal
            WHEN f3.player='.$id.' THEN f3.autogoal
            WHEN f4.player='.$id.' THEN f4.autogoal
            WHEN f5.player='.$id.' THEN f5.autogoal
            ELSE 0
            END as autogoal,
            
            CASE
            WHEN IFNULL(g1.goal,0) + IFNULL(g2.goal,0) + IFNULL(g3.goal,0) + IFNULL(g4.goal,0) + IFNULL(g5.goal,0) +
            IFNULL(f1.autogoal,0) + IFNULL(f2.autogoal,0) + IFNULL(f3.autogoal,0) + IFNULL(f4.autogoal,0) + IFNULL(f5.autogoal,0) = 
            IFNULL(f1.goal,0) + IFNULL(f2.goal,0) + IFNULL(f3.goal,0) + IFNULL(f4.goal,0) + IFNULL(f5.goal,0) +
            IFNULL(g1.autogoal,0) + IFNULL(g2.autogoal,0) + IFNULL(g3.autogoal,0) + IFNULL(g4.autogoal,0) + IFNULL(g5.autogoal,0) 
            THEN 1

            WHEN (t1.player1 = '.$id.' OR t1.player2 = '.$id.' OR t1.player3 = '.$id.' OR t1.player4 = '.$id.' OR t1.player5 = '.$id.')
            AND (IFNULL(g1.goal,0) + IFNULL(g2.goal,0) + IFNULL(g3.goal,0) + IFNULL(g4.goal,0) + IFNULL(g5.goal,0) +
            IFNULL(f1.autogoal,0) + IFNULL(f2.autogoal,0) + IFNULL(f3.autogoal,0) + IFNULL(f4.autogoal,0) + IFNULL(f5.autogoal,0) > 
            IFNULL(f1.goal,0) + IFNULL(f2.goal,0) + IFNULL(f3.goal,0) + IFNULL(f4.goal,0) + IFNULL(f5.goal,0) +
            IFNULL(g1.autogoal,0) + IFNULL(g2.autogoal,0) + IFNULL(g3.autogoal,0) + IFNULL(g4.autogoal,0) + IFNULL(g5.autogoal,0))
            THEN 3

            WHEN (t2.player1 = '.$id.' OR t2.player2 = '.$id.' OR t2.player3 = '.$id.' OR t2.player4 = '.$id.' OR t2.player5 = '.$id.' )
            AND (IFNULL(g1.goal,0) + IFNULL(g2.goal,0) + IFNULL(g3.goal,0) + IFNULL(g4.goal,0) + IFNULL(g5.goal,0) +
            IFNULL(f1.autogoal,0) + IFNULL(f2.autogoal,0) + IFNULL(f3.autogoal,0) + IFNULL(f4.autogoal,0) + IFNULL(f5.autogoal,0) < 
            IFNULL(f1.goal,0) + IFNULL(f2.goal,0) + IFNULL(f3.goal,0) + IFNULL(f4.goal,0) + IFNULL(f5.goal,0) +
            IFNULL(g1.autogoal,0) + IFNULL(g2.autogoal,0) + IFNULL(g3.autogoal,0) + IFNULL(g4.autogoal,0) + IFNULL(g5.autogoal,0))
            THEN 3
            
            ELSE 0 END as points
            
            FROM Matches
            LEFT JOIN Teams   AS t1 ON (Matches.team1 = t1.id) 
            LEFT JOIN Teams   AS t2 ON (Matches.team2 = t2.id)
            LEFT JOIN Goals   AS g1 ON (t1.player1 = g1.player) AND (Matches.id = g1.match)
            LEFT JOIN Goals   AS g2 ON (t1.player2 = g2.player) AND (Matches.id = g2.match)
            LEFT JOIN Goals   AS g3 ON (t1.player3 = g3.player) AND (Matches.id = g3.match)
            LEFT JOIN Goals   AS g4 ON (t1.player4 = g4.player) AND (Matches.id = g4.match)
            LEFT JOIN Goals   AS g5 ON (t1.player5 = g5.player) AND (Matches.id = g5.match)
            LEFT JOIN Goals   AS f1 ON (t2.player1 = f1.player) AND (Matches.id = f1.match)
            LEFT JOIN Goals   AS f2 ON (t2.player2 = f2.player) AND (Matches.id = f2.match)
            LEFT JOIN Goals   AS f3 ON (t2.player3 = f3.player) AND (Matches.id = f3.match)
            LEFT JOIN Goals   AS f4 ON (t2.player4 = f4.player) AND (Matches.id = f4.match)
            LEFT JOIN Goals   AS f5 ON (t2.player5 = f5.player) AND (Matches.id = f5.match)';
}