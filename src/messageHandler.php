<?php
include_once 'logging.php';
include_once 'error.php';
include_once 'rcare.php';
$config = include 'config/config.php';
$enableareaMapping = $config['enable_area_mapping'];
//___________________________________________________________
function areaMapping($id, $array) {
  foreach ($array as $key => $val){
      if ($val['area'] === $id) {
          return $val;
      }
  }
  //return null;
  LogEvent("info","Area dial number is not a match");
  exit;
}
//____________________________________________________________
LogEvent("debug", "Message Handler has started...");

if(isset($_GET['callerId']) && !empty($_GET['callerId'])) {
  global $config;
  $callerid = $_GET['callerId'];

  preg_match("/([^:\s]+):([\w\-\s]+):(\w+)\ (.+)$/",$callerid,$matches);

  $area = $matches[1];
  $room = $matches[2];
  $bed = $matches[3];
  $priority = $matches[4];

  LogEvent("info","Got event from room: $room, bed: $bed, priority: $priority");

  // set the default Rcare type and device in case it's not parsed
  // These values stem from v2.8.8 of the Rcare API
  // 2 - ALARM
  // 3 - CLEAR
  // 7 - DOOR_OPEN
  // 8 - DOOR_CLOSED
  // 10 - INACTIVITY
  // 11 - TIMED_REMINDER
  // 12 - DIALER
  // 13 - WANDER_TAG
  // 14 - LOITER_TAG
  // 15 - WANDER_DOOR
  // 16 - ZONE_RESET
  // 17 - WANDER_RESET
  // 18 - LOITER_RESET
  // 19 - AJAR_RESET
  // 20 - FINGERPRINT
  // 21 - FENCE_ENTER
  // 22 - FENCE_EXIT
  // 23 - HI_TEMP
  // 24 - LOW_TEMP
  // 25 - NORMAL_TEMP
  // 26 - API Alarm
  $type = 3; //Clear
  $device = 2; //This value indicates the device_id of the Rcare device. 2 = bedside
  $device_name = $room;

  if (str_contains($priority, $config['canceled_string'])){
    $type = 3; //This value indicates the alarm type from the API guide. 3 = clear
    $device = 0; //This value indicates the device_id of the Rcare device. 0 = no station
  } elseif ($priority == $config['check_in']) {
    $type = 2; //This value indicates the alarm type from the API guide. 3 = clear
    $device = 1; //This value indicates the device_id of the Rcare device. 0 = no station 
    $device_name = "R5K Room " . $room . " " . $priority;
  } elseif (str_contains($priority, $config['alarm_string'])){
    $device = 5; //This value indicates the device_id of the Rcare device. 1 = pullcord
    $type = 2; //This value indicates the alarm type from the API guide. 26 = API Alarm
    $device_name = "R5K Room " . $room . " " . $priority;
  } else {
    $type = 2; //This value indicates the alarm type from the API guide. 26 = API Alarm
    $device_name = "R5K Room " . $room . " " . $priority;
  }
  
  try {
    if ($enableareaMapping === true) {
      $account = Rcare::getAccountByAddress2($areaMapping,$room);
      }else{
      $account = Rcare::getAccountByName($room);
      }
      Rcare::postAlarm($account,$type,$device);
  } catch (\Throwable $th) {
    ErrorHandler::ThrowHTTPError($th);
  }


} else {
  ErrorHandler::ThrowHTTPError("Error in receiving call: parameter 'callerId' was missing or contained an invalid value.");
}

?>
