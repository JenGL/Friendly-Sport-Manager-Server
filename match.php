<?php
include 'private/db.php';
$db = new DatabaseConnection();

$isAuthorized = include './private/auth.php';
$getMatchDetail = include './private/getMatchDetail.php';
$token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);

header("Content-Type: application/json");
if(isset($_GET['id'])){
    $res = $db->query('SELECT Leagues.name FROM Matches LEFT JOIN Leagues ON (Matches.league = Leagues.id) WHERE Matches.id = ' . $_GET['id'])->fetch_assoc();
    if(isset($res)) {
        $league = $res['name'];
        echo $league;
        if ($isAuthorized($token, $league, $db)) {
            echo $getMatchDetail($_GET['id'], $db);
        } else {
            http_response_code(403);
            $arr = array('error' => 'Not authorized');
            return json_encode($arr);
        }
    } else {
        http_response_code(404);
        $arr = array('error' => 'Match Not Found');
        return json_encode($arr);
    }
} else {
    http_response_code(400);
    $arr = array('error' => 'Bad format');
    return json_encode($arr);
}


$db->close();




/*
 * {
  "data": 1541548800,
  "team_1": [
    {
      "Name": "Nicuola",
      "Goals": 2,
      "Autogoals": 0
    },
    {
      "Name": "Gigi",
      "Goals": 3,
      "Autogoals": 0
    },
    {
      "Name": "Chiassa",
      "Goals": 0,
      "Autogoals": 1
    },
    {
      "Name": "Drago",
      "Goals": 1,
      "Autogoals": 0
    },
    {
      "Name": "Rupi",
      "Goals": 0,
      "Autogoals": 0
    }
  ],
  "team_2": [
    {
      "Name": "Carra",
      "Goals": 0,
      "Autogoals": 1
    },
    {
      "Name": "Mone",
      "Goals": 0,
      "Autogoals": 0
    },
    {
      "Name": "Fraca",
      "Goals": 3,
      "Autogoals": 0
    },
    {
      "Name": "Gilvio",
      "Goals": 1,
      "Autogoals": 0
    },
    {
      "Name": "Robi",
      "Goals": 0,
      "Autogoals": 0
    }
  ],
  "id": 1
}
 */