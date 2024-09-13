<?php

ini_set('error_reporting', 1);
ini_set('memory_limit', '2046M');
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');
set_time_limit(0);

// JWT config
define("JWT_SESSION_TIME", 7200);
define("JWT_SECRET", "df3422cfc9f6e042267138de701cb69649f60b8e5707c6636d66848292a9e3e0");
define("JWT_ISSUER", "tn.eodb.in");
define("JWT_AUD", "tn.eodb.in");
define("JWT_ALGO", "HS512");

// Include ADO DB 
include_once("./libraries/adodb5/adodb.inc.php");

// Database Connection 
class DBConfig { 
    private $host;
    private $db_username ;
    private $db_password;
    private $db_name ;
    function dbConnection() {
        $this->host = "localhost";
        $this->db_username ="root";
        $this->db_password = "";
        $this->db_name = "brap_react";
        try {
            $driver = 'mysqli';
            $dsn_options = '?persist=0&fetchmode=2';
            $dsn = "$driver://$this->db_username:$this->db_password@$this->host/$this->db_name$dsn_options";
            $conn = NewADOConnection($dsn);
            return $conn;
        } catch (PDOException $e) {
            return $e->$conn->errorMsg();
        }
    }
}

include_once("./libraries/phpexcel/PHPExcel.php");
include_once("./libraries/phpexcel/PHPExcel/IOFactory.php");

?>
