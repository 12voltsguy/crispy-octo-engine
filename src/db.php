<?php

include_once 'logging.php';
$config = include('config/config.php');

class DB {
    private static $instance = null;

    private $conn;

    private function __construct()
    {
        if(isset($this->conn)){
            return $this->conn;
        }
        global $config;

        LogEvent("info","trying to connect to db","file");

        $options = array(  
            "UID" => $config['db_user'],
            "PWD" => $config['db_password'],
            "Database" => $config['db_database'],
            "ReturnDatesAsStrings"=> true,
            "CharacterSet" => 'utf-8',
            "trustServerCertificate"=> true
        );
        $this->conn = sqlsrv_connect($config['db_server'], $options);


        if(!isset($this->conn)) {
            print_r(sqlsrv_errors());
            $err = sqlsrv_errors();
            LogEvent("error",implode(" ",$err[0]),"file" );
        }
    }

    public static function getInstance(){
        if (self::$instance == null)
        {
            self::$instance = new DB();
        }

        return self::$instance;
    }

    public static function query(string $query) {
        try {
            $stmt = sqlsrv_query(self::getInstance()->conn,$query);
            
            if ($stmt === false){
                return new Error("Query returned no results");
                LogEvent("info","Query returned no results","file");
            }

            $result = NULL;
            $index = 0;
            while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC )) {
                $result[$index]=$row;
                $index++;
            }            
            
            sqlsrv_free_stmt($stmt);

            return $result;

        } catch (\Throwable $th) {
            print_r(sqlsrv_errors());
            $err = sqlsrv_errors();
            LogEvent("error","error processing SQL " . self::getInstance()->conn,"file");
            LogEvent("error",implode(" ",$err[0]),"file" );
            sqlsrv_free_stmt($stmt);
            throw new Exception("Error Processing SQL query", 1);
        }
    }
}

?>
