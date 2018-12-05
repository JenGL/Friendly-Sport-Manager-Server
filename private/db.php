<?php


class DatabaseConnection {
    public $conn;
    public $transaction_started = false;
    public $transaction_success = false;

    /**
     * DatabaseConnection constructor.
     */
    public function __construct() {
        $config = include('config.php');
        $this->conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['db']);
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function __destruct() {
        $this->conn->close();
    }

    public function close() {
        $this->conn->close();
    }

    public function start_transaction() {
        if (!$this->transaction_started) {
            $this->transaction_success = true;
            $this->transaction_started = true;
            $this->conn->query("START TRANSACTION");
        }
    }

    public function commit_transaction() {
        if ($this->transaction_started) {
            $this->transaction_started = false;
            if($this->transaction_success){
                $this->conn->query("COMMIT");
                return true;
            } else {
                $this->conn->query("ROLLBACK");
                return false;
            }
        }
        return false;
    }

    public function rollback(){
        $this->transaction_started = false;
        $this->conn->query("ROLLBACK");
    }

    public function query($q) {
        $res = $this->conn->query($q);
        $this->update_transaction_status($res);
        if($this->transaction_success){
            echo "SUCCESS\n";
        } else {
            echo "FAIL\n";
        }
        return  $res;
    }

    public function add_account($name, $password) {
        $res = $this->conn->query('INSERT INTO Account (username, password) VALUES ("'.$name.'","'.$password.'")');
        $this->update_transaction_status($res);
        return $this->conn->lastInsertId();
    }


    public function link_account_to_league($account_id, $league_id) {
        $res = $this->conn->query('INSERT INTO Account (username, password) VALUES ("'.$account_id.'","'.$league_id.'")');
        $this->update_transaction_status($res2);
        return $res;
    }





    public function insert_player($name) {
        $res = $this->conn->query('INSERT INTO Players (nome) VALUES ("'.$name.'")');
        $this->update_transaction_status($res);
        return $this->select_player($name);
    }

    public function insert_team_sql($team)
    {
        $query = 'INSERT INTO Teams (Player1, Player2, Player3, Player4, Player5) SELECT * FROM (SELECT ' . $team[0]->id . ',' . $team[1]->id . ',' . $team[2]->id . ',' . $team[3]->id . ',' . $team[4]->id . ') AS tmp 
        WHERE NOT EXISTS ( SELECT id FROM Teams WHERE Player1 = ' . $team[0]->id . ' AND Player2 = ' . $team[1]->id . ' AND Player3 = ' . $team[2]->id . ' AND Player4 = ' . $team[3]->id . ' AND Player5 = ' . $team[4]->id . ') 
        LIMIT 1';
        $res = $this->conn->query($query);
        $this->update_transaction_status($res);
        return $this->select_team_sql($team);
    }

    public function insert_match_sql($t1_id, $t2_id, $data)
    {
        $res = $this->conn->query('INSERT INTO Matches (`Team1`, `Team2`, `Data`) VALUES (' . $t1_id . ',' . $t2_id . ',"' . $data . '")');
        $this->update_transaction_status($res);
        return $this->select_match_sql($t1_id, $t2_id, $data);
    }

    public function insert_goals_sql($match, $p_id, $goal, $autogoal, $result)
    {
        if ($goal + $autogoal > 0) {
            $res = $this->conn->query('INSERT INTO Goals (`player`, `match`, `goal`, `autogoal`) VALUES (' . $p_id . ',' . $match . ',' . $goal . ',' . $autogoal . ')');

            $this->update_transaction_status($res);
        }

        if ($result < 0) {
            $res2 = $this->conn->query('UPDATE Players SET goal = goal + ' . $goal . ', autogoal = autogoal + ' . $autogoal . ', loss = loss + 1 WHERE id = ' . $p_id);
        } else if ($result > 0) {
            $res2 = $this->conn->query('UPDATE Players SET goal = goal + ' . $goal . ', autogoal = autogoal + ' . $autogoal . ', win = win + 1 WHERE id = ' . $p_id);
        } else {
            $res2 = $this->conn->query('UPDATE Players SET goal = goal + ' . $goal . ', autogoal = autogoal + ' . $autogoal . ', pair = pair + 1 WHERE id = ' . $p_id);
        }

        $this->update_transaction_status($res2);
        return $this->select_goals_sql($p_id, $match);
    }

    private function update_transaction_status($res)
    {
        if ($this->transaction_started) {
            $this->transaction_success = $res && $this->transaction_started;
        }
    }

    public function select_player($name){
        return $this->conn->query('SELECT * FROM Players WHERE nome = "'.$name.'"');
    }

    public function select_team_sql($team)
    {
        return $this->conn->query('SELECT * FROM Teams WHERE Player1 = ' . $team[0]->id . ' AND Player2 = ' . $team[1]->id . ' AND Player3 = ' . $team[2]->id . ' AND Player4 = ' . $team[3]->id . ' AND Player5 = ' . $team[4]->id);
    }

    public function select_match_sql($t1_id, $t2_id, $data)
    {
        return $this->conn->query('SELECT * FROM Matches WHERE `Team1` = ' . $t1_id . ' AND `Team2` = ' . $t2_id . ' AND `Data` = "' . $data . '"');
    }

    public function select_goals_sql($player, $match)
    {
        return $this->conn->query('SELECT * FROM Goals WHERE player = ' . $player . 'AND `match` = ' . $match);
    }

    public function select_all_players_sql()
    {
        return $this->conn->query('SELECT * FROM Players');
    }

    public function select_all_matches_sql()
    {
        return $this->conn->query('SELECT * FROM Matches');
    }

    public function select_team_players($team)
    {
        return $this->conn->query('SELECT * FROM Teams WHERE id = '.$team);
    }

    public function select_match_join_players(){
        $query = "SELECT Matches.Data, 
                  a1.nome t1_p1_name, a2.nome t1_p2_name, a3.nome t1_p3_name, a4.nome t1_p4_name, a5.nome t1_p5_name,
                  b1.nome t2_p1_name, b2.nome t2_p2_name, b3.nome t2_p3_name, b4.nome t2_p4_name, b5.nome t2_p5_name,
                  ifnull(g1.goal,0) + ifnull(g2.goal,0) + ifnull(g3.goal,0) + ifnull(g4.goal,0) + ifnull(g5.goal,0) +
                  ifnull(f1.autogoal,0) + ifnull(f2.autogoal,0) + ifnull(f3.autogoal,0) + ifnull(f4.autogoal,0) + ifnull(f5.autogoal,0) as t1_score, 
                  ifnull(f1.goal,0) + ifnull(f2.goal,0) + ifnull(f3.goal,0) + ifnull(f4.goal,0) + ifnull(f5.goal,0) +
                  ifnull(g1.autogoal,0) + ifnull(g2.autogoal,0) + ifnull(g3.autogoal,0) + ifnull(g4.autogoal,0) + ifnull(g5.autogoal,0) as t2_score
                  FROM Matches
                  LEFT JOIN Teams t1 ON (Matches.Team1 = t1.id) 
                  LEFT JOIN Teams t2 ON (Matches.Team2 = t2.id)
                  LEFT JOIN Players a1 ON (t1.Player1 = a1.id)
                  LEFT JOIN Players a2 ON (t1.Player2 = a2.id)
                  LEFT JOIN Players a3 ON (t1.Player3 = a3.id)
                  LEFT JOIN Players a4 ON (t1.Player4 = a4.id)
                  LEFT JOIN Players a5 ON (t1.Player5 = a5.id)
                  LEFT JOIN Players b1 ON (t2.Player1 = b1.id)
                  LEFT JOIN Players b2 ON (t2.Player2 = b2.id)
                  LEFT JOIN Players b3 ON (t2.Player3 = b3.id)
                  LEFT JOIN Players b4 ON (t2.Player4 = b4.id)
                  LEFT JOIN Players b5 ON (t2.Player5 = b5.id)
                  LEFT JOIN Goals g1 ON (a1.id = g1.player) AND (Matches.id = g1.match)
                  LEFT JOIN Goals g2 ON (a2.id = g2.player) AND (Matches.id = g2.match)
                  LEFT JOIN Goals g3 ON (a3.id = g3.player) AND (Matches.id = g3.match)
                  LEFT JOIN Goals g4 ON (a4.id = g4.player) AND (Matches.id = g4.match)
                  LEFT JOIN Goals g5 ON (a5.id = g5.player) AND (Matches.id = g5.match)
                  LEFT JOIN Goals f1 ON (b1.id = f1.player) AND (Matches.id = f1.match)
                  LEFT JOIN Goals f2 ON (b2.id = f2.player) AND (Matches.id = f2.match)
                  LEFT JOIN Goals f3 ON (b3.id = f3.player) AND (Matches.id = f3.match)
                  LEFT JOIN Goals f4 ON (b4.id = f4.player) AND (Matches.id = f4.match)
                  LEFT JOIN Goals f5 ON (b5.id = f5.player) AND (Matches.id = f5.match)
                  ORDER BY Matches.Data DESC";

        return $this->conn->query($query);
    }
}