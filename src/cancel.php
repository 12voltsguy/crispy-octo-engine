<?php
include_once 'logging.php';
include_once 'error.php';
include_once 'rcare.php';
$config = include 'config/config.php';
$type = 3; //This value indicates the alarm type from the API guide. 3 = clear
$device = 0; //This value indicates the device_id of the Rcare device. 0 = no station
$serverName = "000.000.000.000";
$connectionInfo = array( "Database"=>"r5000", "UID"=>"User", "PWD"=>"Password","TrustServerCertificate"=>true);
$conn = sqlsrv_connect( $serverName, $connectionInfo );
if( $conn === false ) {
    die( print_r( sqlsrv_errors(), true));
}

$sql = "Select AceTransaction.Room From AceTransaction
        Inner Join AceDetail on AceTransaction.AceTransactionId = AceDetail.AceTransactionId 
        where ISOn = 0 AND Description LIKE '%Cancelled' AND EventDateTime > DATEADD(minute, -5, CURRENT_TIMESTAMP)";
$stmt = sqlsrv_query( $conn, $sql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}

while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
    $room = $row['Room'];
    try{ $account = Rcare::getAccountByName("R5k Room " . $room);
         //echo $account,$type,$device." ".$room."<br />";
    if (isset($account) && $account !== '') {
     Rcare::postAlarm($account,$type,$device);
        //echo $account,$type,$device;
       //echo $account,$type,$device." ".$room."<br />";
     }
    } catch (\Throwable $th) {
    ErrorHandler::ThrowHTTPError($th);
  }
}

sqlsrv_free_stmt( $stmt);
?>
