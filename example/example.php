<?php

require_once 'vendor/autoload.php';

try {
    // connecting to jobd
    $client = new jobd\Client(jobd\Client::MASTER_PORT);

    // asking master to ask workers responsible for server1 to poll new jobs
    $client->poke(['server1']);
} catch (Exception $e) {
    die($e->getMessage());
}

// closing connection
$client->close();
