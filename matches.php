<?php
include 'private/db.php';
$handleOptions = include './private/options.php';
$handleOptions();
$db = new DatabaseConnection();

$isAuthorized = include './private/auth.php';
$getMatches = include './private/getMatches.php';
$token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
if (isset($_GET['league'])) {
    if ($isAuthorized($token, $_GET['league'], $db)) {
        echo $getMatches($_GET['league'], $db);
    } else {
        http_response_code(403);
        $arr = array('error' => 'Not authorized');
        echo json_encode($arr);
    }
} else {
    http_response_code(400);
    $arr = array('error' => 'Bad Format');
    echo json_encode($arr);
}

$db->close();






/*
[
{
"data" : 1541548800,
"team_1": ["Nicuola", "Gigi", "Rupi", "Chiassa", "Carra"],
"team_2":  ["Nicuola", "Gigi", "Rupi", "Chiassa", "Carra"],
"team_1_score": 2,
"team_2_score": 3,
"id" : 1
},
{
"data" : 1542153600,
"team_1": ["Nicuola", "Gigi", "Rupi", "Chiassa", "Carra"],
"team_2":  ["Nicuola", "Gigi", "Rupi", "Chiassa", "Carra"],
"team_1_score": 4,
"team_2_score": 2,
"id" : 2
},
{
"data" : 1542758400,
"team_1": ["Nicuola", "Gigi", "Rupi", "Chiassa", "Carra"],
"team_2":  ["Nicuola", "Gigi", "Rupi", "Chiassa", "Carra"],
"team_1_score": 3,
"team_2_score": 1,
"id" : 3
}
]
*/