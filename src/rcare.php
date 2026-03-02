<?php

include_once 'db.php';
include_once 'logging.php';
$config = include('config/config.php');

class Rcare {
    // Pull in config file to pull options
    
    private static $instance = null;
    
    private $accessToken;
    private $refreshToken;
    private $tokenExpiration;

    private function __construct(){
        $this->getToken();
    }


    ## Get an API Token ##
    private function getToken(){
        global $config;

        if (
                isset($this->accessToken) && 
                isset($this->tokenExpiration) &&
                $this->tokenExpiration < new DateTime()
            ) {
            LogEvent("info","Existing Rcare token is still valid. Using it");
            return $this->accessToken;
        }


        LogEvent("info","Requesting a new Rcare auth token");

        $curl = curl_init();

        $payload = array(
            "username" => $config['rcare_api_user'],
            "password" => $config['rcare_api_password']
          );        

        curl_setopt_array($curl, array(
            CURLOPT_URL => $config["rcare_api_server"] . "/api/auth/user",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));
        LogEvent("info","Succesfully logged in to rcare");
        $result = curl_exec($curl);
        $object = json_decode($result);

        // Store the token into the class variables
        $this->accessToken = $object->accessToken;
        $this->refreshToken = $object->refreshToken;

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone($config["timezone"]));
        // Add the Rcare API returned ttl to the timestamp for the actual expiration
        $date->add(DateInterval::createFromDateString($object->ttl . ' seconds'));

        $this->tokenExpiration = $date;

        curl_close($curl);

        LogEvent("info","Got Rcare auth token '" . $this->accessToken . "'");
        LogEvent("info","Token expires at: " . $date->format("c"));

        // LogEvent("info","Logging token to database");
        // DB::query("INSERT INTO RcareToken (authtoken, refreshtoken) VALUES ('$this->accessToken', '$this->refreshToken')");
        
        return $this->accessToken;
    }

    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new Rcare();
        }

        return self::$instance;
    }

    public static function getAccounts()
    {
        global $config;
        
        LogEvent("info", "Starting update of accounts from Rcare's API");
        
        $payload = array(
            "username" => $config["rcare_api_user"],
            "password" => $config["rcare_api_password"]
          );
        
          /*Start the PHP CURL session to pass HTTP API POST information to Rcare server. This includes the $authtoken to allow the api call___________________________*/
          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => ("http://" . $config["rcare_api_server"] . "/api/account"),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
              "Content-Type: application/json",
              "Authorization: Bearer " . self::getInstance()->accessToken,
              "Content-Type: application/json"
            ),
          ));
          $response = curl_exec($curl);
          curl_close($curl);
          //__Takes PHP CURL $response and decodes json array, then explodes the array.________________________ 
          //echo $response;
          $response_data = json_decode($response);
        
          // Get all accounts out of the response_data object
          $accounts = $response_data->accounts;
          $accounts = array_slice($accounts, 0,);
        
          // Traverse array and print employee data
          foreach ($accounts as $account) {
            $id = $account->account;
            $name = $account->name;
            $address1 = $account->address1;
            $address2 = $account->address2;

            LogEvent("info","Importing Account: ID:" . $id . " Name: " . $name . "  address1:" . $address1 . "  address2: " . $address2);
        
            DB::query("
                IF EXISTS (SELECT 1 FROM rcare_account WHERE account='$id')
                BEGIN
                    UPDATE rcare_account 
                    SET name = '$name', address1 = '$address1', address2 = '$address2' 
                    WHERE account = '$id'
                END 
                ELSE 
                BEGIN 
                    INSERT INTO rcare_account (account, name, address1, address2)
                    VALUES ('$id','$name','$address1','$address2')
                    
                END
            ");

          }
    }

    public static function getAccountByName($name) {
        LogEvent("info","Getting account by name: $name");
        
        try {
            $result = DB::query("SELECT account FROM rcare_account WHERE name LIKE '%$name%'");
            
            LogEvent("info","Got account: " . $result[0]['account']);

            return $result[0]['account'];
        } catch (\Throwable $th) {
            LogEvent("error","Couldn't get account by name: $name");
            return $th;
        }

    }

    public static function getAccountByAddress2($areaMapping,$room) {
      LogEvent("info","Matching Rcare account by Responder area & room: ". $areaMapping." ". $room);
      
        $result = DB::query("SELECT account FROM rcare_account WHERE address1='$room' and address2 LIKE '%$areaMapping%'");
          if ($result > 0) {
            LogEvent("info","Got account: " . $result[0]['account']);
            return $result[0]['account'];
        } else {
            LogEvent("info","No matching Rcare account found ". $areaMapping." ". $room);
        }
  }

    public static function createAccount($name,$address1,$notify_group){
        global $config;

        LogEvent("info","Posting an account to the Rcare API. Name: $name, address1: $address1, notify_group: $notify_group");
        $curl = curl_init();

        $payload = array(
            "name" => $name,
            "address1" => $address1,
            "notify_group" => $notify_group,

            // other possible values
            // "address2" => $address2,
            // "phone" => $phone,
            // "notes" => $notes,
        );
      
        curl_setopt_array($curl, array(
          CURLOPT_URL => ($config["rcare_api_server"] . "/api/alarm"),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($payload),
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . self::getInstance()->accessToken
          ),
        ));
      
        $response = curl_exec($curl);
      
        curl_close($curl);

        LogEvent("info","Rcare API response: $response");
        return $response;
    }

    public static function postAlarm($account,$type,$device){
        global $config;

        LogEvent("info","Posting an alarm to the Rcare API account: $account, type: $type, device: $device");
        $curl = curl_init();

        $payload = array(
          "account" => $account,
          "type" => $type,
          "device" => $device
        );
      
        curl_setopt_array($curl, array(
          CURLOPT_URL => ($config["rcare_api_server"] . "/api/alarm"),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($payload),
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . self::getInstance()->accessToken
          ),
        ));
      
        $response = curl_exec($curl);
      
        curl_close($curl);

        LogEvent("info","Rcare API response: $response");
        return $response;
    }
}

?>