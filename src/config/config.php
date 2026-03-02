<?php

return array(
    # LOGGING #
    // log levels: info, error
    'log_verbosity' => (getenv('LOG_VERBOSITY') !== false ? getenv('LOG_VERBOSITY') : "error"),
    'log_location' => (getenv('LOG_LOCATION') !== false ? getenv('LOG_LOCATION') : "logs/"),

    # DATABASE #
    'db_server' => (getenv('DB_SERVER') !== false ? getenv('DB_SERVER') : "127.0.0.1"),
    'db_user' => (getenv('DB_USER') !== false ? getenv('DB_USER') : "sa"),
    'db_password' => (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "Your DB Password"),
    'db_database' => (getenv('DB_DATABSE') !== false ? getenv('DB_DATABASE') : "Alertiv"),

    # RCARE API #
    'rcare_api_server' => (getenv('RCARE_API_SERVER') !== false ? getenv('RCARE_API_SERVER') : "10.1.10.206"),
    'rcare_api_user' => (getenv('RCARE_API_USER') !== false ? getenv('RCARE_API_USER') : "Your Rcare User"),
    'rcare_api_password' => (getenv('RCARE_API_PASSWORD') !== false ? getenv('RCARE_API_PASSWORD') : "Your Rcare Password"),

    #Timezone
    'timezone' => (getenv('TIMEZONE') !== false ? getenv('TIMEZONE') : "Etc/UTC"),
    
    # R5K Options #
    'canceled_string' => (getenv('CANCELED_STRING') !== false ? getenv('CANCELED_STRING') : "Canceled"),
    'alarm_string' => (getenv('ALARM_STRING') !== false ? getenv('ALARM_STRING') : "Staff Asist"),
    'check_in' => (getenv('CHECKIN_STRING') !== false ? getenv('CHECKIN_STRING') : "Check In"),

    #Area Mapping - true = "ON" or false = "OFF"#
    'enable_area_mapping' => false,

    'areaMapping' => array(
        array('friendlyName' => 'R5K Room','dial' => '10','area' => 'MOR_'),
        array('friendlyName' => 'R5K Room','dial' => '20','area' => 'ENDO_'),
        ),
);

?>
