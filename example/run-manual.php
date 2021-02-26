<?php

require_once 'vendor/autoload.php';

// connecting to mysql
$db = new mysqli();
if (!$db->real_connect('10.211.55.6', 'jobd', 'password', 'jobd'))
    die('Failed to connect.');

// adding manual task
$target = 'server1';
$time = time();
if (!$db->query("INSERT INTO jobs (target, slot, time_created, status) VALUES ('server1', 'normal', $time, 'manual')"))
    die($db->error);

$id = $db->insert_id;

try {
    // connecting to jobd
    $client = new jobd\Client(jobd\Client::WORKER_PORT);

    // launching task
    $result = $client->runManual($id);

    // printing the result
    print_r($result->getData());
} catch (Exception $e) {
    die($e->getMessage());
}
