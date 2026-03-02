<?php
// Settings
$address = '127.0.0.1';
$port = 6000;

// Define a bunch of char strings we'll need later
$EOT = chr(4);
$ACK = chr(6);
$CR = chr(13);
$ESC = chr(27);
$PG1 = $ESC . "PG1" . $CR;
$MSG_ACK = $CR . $ACK . $CR . $ESC . '[p' . $CR;

// Create Socket
if (($socket = socket_create(AF_INET, SOCK_STREAM, 0)) === false) {
    echo "Couldn't create socket".socket_strerror(socket_last_error())."\n";
}

// Bind
if (socket_bind($socket, $address, $port) === false) {
    echo "Bind Error ".socket_strerror(socket_last_error($sock)) ."\n";
}

// Start listening
if (socket_listen($socket, 5) === false) {
    echo "Listen Failed ".socket_strerror(socket_last_error($socket)) . "\n";
}

//Listen for connections
while (true) {
    if (($client = socket_accept($socket)) === false) {
        echo "Error: socket_accept: " . socket_strerror(socket_last_error($socket)) . "\n";
        break;
    }

    // Create a blank messages array that we'll write into
    $messages = array();

    // Listen to client input
    while(true) {
        if(false === ($buf = socket_read($client, 1024, PHP_NORMAL_READ))) {
            echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($client)) . "\n";
            break;
        }

        if ($buf == $CR){
            // echo "\nSending ID=";
            // Send ID= for TAP continuation
            $msg = 'ID=';
            socket_write($client, $msg, strlen($msg));
            continue;
        }

        if ($buf == $PG1){
            // echo "\nSending MSG_ACK";
            // Send ACK of message
            $msg = $MSG_ACK;
            socket_write($client, $msg, strlen($msg));
            continue;
        }

        if ($buf == ($EOT . $CR)){
            // break on end of transmission - EOT char.
            break;
        }

        if (!$buf == trim($buf)) {
            // Print unknown message in terminal
            echo "\nUnknown Message: " . json_encode($buf);
            continue;
        }

        // Add this message to array for processing later
        array_push($messages, ($buf));

        // Reply to sender each time we received
        $msg = $ACK . $CR;
        socket_write($client, $msg, strlen($msg));
    };

    // If there are messages to process, do that now
    if (count($messages) > 1){
        echo "\n\n=======================";
        echo "\nCAPCODE: " . trim($messages[0]);
        echo "\nMessage: ";
        for ($i = 1; $i < count($messages)-1 ; $i++){
            $i > 1 ?: "/n";
            echo trim($messages[$i]);
        }
        echo "\n=======================";


        // WIP - Add handoff to Alertiv MessageHandler here
    }

    // Clear messages array
    $messages = array();

    socket_close($client);
};

// If the while exits for any reason, close the socket.
socket_close($socket);
?>