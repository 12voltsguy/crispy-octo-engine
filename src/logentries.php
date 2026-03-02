<!doctype html>
<html class="h-full bg-gray-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans h-full">
<div class="min-h-full w-full overflow-hidden">

<?php

$topParam = isset($_GET["top"]) ? $_GET["top"] : null;

$config = include 'config/config.php';
$conn = new PDO("sqlsrv:TrustServerCertificate=1;server=" . $config["db_server"] . "; Database=" . $config["db_database"]."",$config["db_user"],$config["db_password"]);

$stmt = $conn->prepare('EXEC getLogs @top = :top');
$stmt->bindValue(":top",( ($topParam) ? $topParam : '20' ));
$stmt->execute();
$logEntries = $stmt->fetchAll();

$conn = null;

echo("
        <table class=\"table-fixed w-full border-collapse border border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 text-sm shadow-sm\">
            <tr>
                <th class=\"p-2 w-1/5 border border-slate-300 dark:border-slate-600 font-semibold text-slate-900 dark:text-slate-200 text-left\">Timestamp</th>
                <th class=\"p-2 w-24 border border-slate-300 dark:border-slate-600 font-semibold text-slate-900 dark:text-slate-200 text-left\">Level</th>
                <th class=\"p-2 border border-slate-300 dark:border-slate-600 font-semibold text-slate-900 dark:text-slate-200 text-left\">Message</th>
            </tr>
                "
);

foreach($logEntries as $log){
    echo("
        <tr>
            <td class=\"px-2 w-1/3 border border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400\">" . $log["event_time"] . "</td>
            <td class=\"px-2 border border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400\">" . $log["error_level"] . "</td>
            <td class=\"px-2 border border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400 truncate\">" . $log["message"] . "</td>
        </tr>
    ");
}

echo("
        </table>
    </div>"
);

// include_once 'db.php';
// /*//New DateTime object representing today's date.
// $currentDate = new DateTime();
// //Use the sub function to subtract a DateInterval
// $yesterdayDT = $currentDate->sub(new DateInterval('P1D'));
// //Get yesterday's date in a YYYY-MM-DD format.
// $yesterday = $yesterdayDT->format(DateTime::ATOM);
// //Print it out.
// //echo '<br/>'.$yesterday;
// */
// $Start = 0;

// $contents=file_get_contents("logs/logfile.txt", FALSE, NULL, $Start, NULL);
// $rows=explode("\n",$contents);
// array_shift($rows);

// foreach($rows as $row => $data)
//     {
//     //get row data
//     $row_data = explode("\t", $data);
        
//     $info[$row]['time']         =$row_data[0];
//     $info[$row]['level']        =$row_data[1];
//     $info[$row]['description']  =$row_data[2];

//     echo 'Row ' . $row . ' TIME: ' . $info[$row]['time'] .'<br />' 
//     . ' LEVEL: ' . $info[$row]['level'] .'<br />' 
//     . ' DESCRIPTION: ' . $info[$row]['description'] . '<br/><br/><br/>'; 
    
//     // DB::query("
//     //     IF EXISTS (SELECT 1 FROM event_log WHERE log_time='$row_data[0]')
//     //     BEGIN
//     //         UPDATE event_log 
//     //         SET log_time = '$row_data[0]', error_level = '$row_data[1]', message = '$row_data[2]' 
//     //         WHERE log_time='$row_data[0]'
//     //     END 
//     //     ELSE 
//     //     BEGIN 
//     //         INSERT INTO event_log (error_level, message, log_time)
//     //         VALUES ('$row_data[1]','$row_data[2]','$row_data[0]')
            
//     //     END
//     // ");
    
//     }
?>
</div>
</body>
</html>