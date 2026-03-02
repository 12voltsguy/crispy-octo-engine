<?php

class ErrorHandler
{
    public static function ThrowHTTPError(string $msg){
        LogEvent("error",$msg);

        http_response_code(404);
        header("Content-Type: application/json");
        echo(json_encode(array(
          "error"=>$msg
        )));
    }
}


?>