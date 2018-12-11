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
        $this->conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['db']);
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $this->conn->autocommit(FALSE);
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
            $this->conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
        }
    }

    private function update_transaction_status($res)
    {
        if ($this->transaction_started) {
            $this->transaction_success = $res && $this->transaction_started && $this->transaction_success;
        }
    }

    public function commit_transaction() {
        if ($this->transaction_started) {
            $this->transaction_started = false;
            if($this->transaction_success) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                return false;
            }
        }
        return false;
    }

    public function rollback() {
        $this->transaction_started = false;
        $this->conn->rollback();
    }

    public function query($q) {
        $res = $this->conn->query($q);
        $this->update_transaction_status($res);
        /*if(!$this->transaction_success) {
            echo $this->transaction_success." FAILED: ".$q."\n";
        }*/
        return  $res;
    }

    public function lastInsertId() {
        return $this->conn->insert_id;
    }
}