<?php
return function ($league, $db) {
    $q = buildQuery($league);
    $res_matches_rows = $db->query($q);
    $arr = array();
    while ($row = $res_matches_rows->fetch_assoc()) {
        array_push($arr, transformRes($row));
    }
    http_response_code(200);
    return json_encode($arr);
};

function transformRes($row)
{
    return array('id' => $row['id'], 'data' => $row['Data'],
        "team_1" => array(getPlayerObj($row, 't1_p1'),getPlayerObj($row,'t1_p2'),getPlayerObj($row,'t1_p3'),getPlayerObj($row,'t1_p4'),getPlayerObj($row,'t1_p5')),
        "team_2" => array(getPlayerObj($row,'t2_p1'),getPlayerObj($row,'t2_p2'),getPlayerObj($row,'t2_p3'),getPlayerObj($row,'t2_p4'),getPlayerObj($row,'t2_p5')));
}

function getPlayerObj($row, $p){
    return array("name" => $row[$p.'_name'],"goal" => $row[$p.'_goal'],"autogoal" => $row[$p.'_autogoal']);
}

function buildQuery($id) {
    return 'SELECT Matches.id, Matches.Data, 
            a1.name t1_p1_name, a2.name t1_p2_name, a3.name t1_p3_name, a4.name t1_p4_name, a5.name t1_p5_name,
            b1.name t2_p1_name, b2.name t2_p2_name, b3.name t2_p3_name, b4.name t2_p4_name, b5.name t2_p5_name,
            IFNULL(g1.goal, 0) t1_p1_goal, IFNULL(g2.goal, 0) t1_p2_goal, IFNULL(g3.goal, 0) t1_p3_goal, IFNULL(g4.goal, 0) t1_p4_goal, IFNULL(g5.goal, 0) t1_p5_goal,
            IFNULL(g1.autogoal,0) t1_p1_autogoal, IFNULL(g2.autogoal,0) t1_p2_autogoal, IFNULL(g3.autogoal,0) t1_p3_autogoal, IFNULL(g4.autogoal,0) t1_p4_autogoal, IFNULL(g5.autogoal,0) t1_p5_autogoal,
            IFNULL(f1.goal,0) t2_p1_goal, IFNULL(f2.goal,0) t2_p2_goal, IFNULL(f3.goal,0) t2_p3_goal, IFNULL(f4.goal,0) t2_p4_goal, IFNULL(f5.goal,0) t2_p5_goal,
            IFNULL(f1.autogoal,0) t2_p1_autogoal, IFNULL(f2.autogoal,0) t2_p2_autogoal, IFNULL(f3.autogoal,0) t2_p3_autogoal, IFNULL(f4.autogoal,0) t2_p4_autogoal, IFNULL(f5.autogoal,0) t2_p5_autogoal
            FROM Matches
            LEFT JOIN Leagues AS l ON (Matches.league = l.id) 
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
            LEFT JOIN Goals   AS f5 ON (b5.id = f5.player) AND (Matches.id = f5.match)
            WHERE Matches.id = ' . $id;
}