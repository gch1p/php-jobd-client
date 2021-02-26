<?php

require_once 'vendor/autoload.php';

try {
    $client = new jobd\Client(jobd\Client::MASTER_PORT);
} catch (Exception $e) {
    die($e->getMessage());
}

// $status = $client->status();
// var_dump($status->getData());

var_dump($client->poke(['server1']));
