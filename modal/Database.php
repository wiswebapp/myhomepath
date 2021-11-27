<?php
namespace modal;

require_once(__DIR__ . '/../env.php');

class Database {

    public $conn;

    public function __construct() {
        $connection = new \mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if(! $connection) {
            exit("Database setup failed");
        }
        $this->conn = $connection;
    }

    public function connect() {
        return $this->conn;
    }

    public function disConnect() {
        $this->conn->close();
        return true;
    }

}