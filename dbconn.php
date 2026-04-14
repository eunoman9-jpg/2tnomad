<?php
class Database {
    private $host;
    private $username;
    private $password;
    private $dbname;

    public $conn;

    // Constructor: connect automatically when object is created
    public function __construct() {
        $this->host     = getenv('MYSQLHOST')     ?: 'localhost';
        $this->username = getenv('MYSQLUSER')     ?: 'root';
        $this->password = getenv('MYSQLPASSWORD') ?: '';
        $this->dbname   = getenv('MYSQLDATABASE') ?: '2tnomad';
        $this->connect();
    }

    // Connect method
    private function connect() {
        $this->conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->dbname
        );

        // Check for connection errors
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Optional: set charset to UTF-8
        $this->conn->set_charset("utf8");
    }

    // Close connection
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>