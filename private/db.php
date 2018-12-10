<?php


class DatabaseConnection {
    public $conn;
    public $transaction_started = false;
    public $transaction_success = true;

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

    private function update_transaction_status($res)
    {
        if ($this->transaction_started) {
            $this->transaction_success = $res && $this->transaction_started;
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

    public function rollback() {
        $this->transaction_started = false;
        $this->conn->query("ROLLBACK");
    }

    public function query($q) {
        $res = $this->conn->query($q);
        $this->update_transaction_status($res);
        if(!$this->transaction_success) {
            echo $this->transaction_success." FAILED: ".$q."\n";
        }
        return  $res;
    }

    public function lastInsertId() {
        return $this->conn->insert_id;
    }
}