<?php

class ConnectionString{
   protected $serverName = "BURKE\SQLEXPRESS"; // Host and instance
   protected $connectionOptions = ["Database" => "EcommerceDB", "Uid" => "", "PWD" => ""];
   public $connection;

    function __construct(){
    $this->connection = sqlsrv_connect($this->serverName, $this->connectionOptions);
    if (!$this->connection) {
        die(print_r(sqlsrv_errors(), true));
    }
   }
   public function query(string $query, array $params = []) {

   }

}


?>