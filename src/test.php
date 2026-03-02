<?php
include 'rcare.php';

echo(Rcare::getAccountByName('Someone New'));

$message = DateTime::createFromFormat(DateTime::ISO8601, date("c"))->format(DateTime::ATOM);

echo '<br/>'.$message;

//New DateTime object representing today's date.
$currentDate = new DateTime();

//Use the sub function to subtract a DateInterval
$yesterdayDT = $currentDate->sub(new DateInterval('P1D'));

//Get yesterday's date in a YYYY-MM-DD format.
$yesterday = $yesterdayDT->format(DateTime::ATOM);

//Print it out.
echo '<br/>'.$yesterday;
?>