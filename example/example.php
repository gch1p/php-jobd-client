<?php

require_once 'vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // connecting to jobd
    $client = new jobd\Client(jobd\Client::MASTER_PORT);

    // asking master to ask workers responsible for server1 to poll new jobs
    $resp = $client->poke(['server1']);
    var_dump($resp);
} catch (Exception $e) {
    die("error: ".$e->getMessage());
}

// closing connection
$client->close();
