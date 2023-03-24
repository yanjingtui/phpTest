<?php
$socket = stream_socket_client("tcp://127.0.0.1:4444", $errno, $errstr);

if (!$socket) {
    echo "$errstr ($errno)<br />\n";
} else {
    
    while (true)
    {   
        echo fread($socket, 1024) . "\n";
        $message = trim(fgets(STDIN));
        fwrite($socket, $message);
    }
}
?>
