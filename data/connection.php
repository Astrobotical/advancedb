<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
class ConnectionString
{
    protected $serverName = "BURKE\SQLEXPRESS"; // Host and instance
    protected $connectionOptions = ["Database" => "EcommerceDB", "Uid" => "", "PWD" => ""];
    public $connection;

    function __construct()
    {
        try {
            $this->connection = sqlsrv_connect($this->serverName, $this->connectionOptions);
            if (!$this->connection) {
                echo "False";
            }
        } catch (Exception $e) {
            echo "False";
        }
    }
    public function query(string $query, array $params = []) {}
}
