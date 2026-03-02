<?php
    
    $config = include 'config/config.php';

    function LogEvent($level,$msg,$logtype="db") {
        global $config;
         
        // if ($level == "info" && $config["log_verbosity"] == "error") {
        //     exit;
        // }
        $timestamp = DateTime::createFromFormat(DateTime::ISO8601, date("c"))->format(DateTime::ATOM);
        $message = $timestamp . "\t" . $level . "\t" . $msg;
        
        try {
            if ($logtype == "file") {
                file_put_contents("logs/logfile.txt",$message."\n", FILE_APPEND);
            } else {
                $conn = new PDO("sqlsrv:TrustServerCertificate=1;server=" . $config["db_server"] . "; Database=" . $config["db_database"],$config["db_user"],$config["db_password"]);
            
                $stmt = $conn->prepare('
                INSERT INTO Alertiv.dbo.event_log (error_level, message, log_time)
                VALUES (:level,:msg,:timestamp)
                ');
                $stmt->bindValue(":level",$level);
                $stmt->bindValue(":msg",$msg);
                $stmt->bindValue(":timestamp",$timestamp);
                $stmt->execute();

                $conn = null;
            
            
            }
        } catch (\Throwable $th){
            
            throw new Exception("Can't log", 1);
        }
       
    }
        

?>