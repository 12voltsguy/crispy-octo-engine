<?php
include_once 'logging.php';
include_once 'error.php';
include_once 'db.php';
$config = include 'config/config.php';
LogEvent("info","Initiating event_log trim");
$eventlogTrim = DB::query("DECLARE @i int = 1;
DECLARE @id int;
SET @id = COALESCE((
    SELECT TOP(1) mt.id
    FROM dbo.event_log mt
    WHERE mt.event_time < DATEADD(DAY,-90,GETDATE())
    ORDER BY mt.event_time DESC
    ), 0);
WHILE @i > 0
BEGIN
    DELETE TOP(1000) mt
    FROM dbo.event_log mt
    WHERE mt.ID <= @ID;
    SET @i = @@ROWCOUNT;
END");
LogEvent("info","event_log trim maintenance complete");
?>
