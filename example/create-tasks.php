<?php

$db = new mysqli();
if (!$db->real_connect('10.211.55.6', 'jobd', 'password', 'jobd'))
    die('Failed to connect.');

$target = 'server1';
$slots = ['low', 'normal', 'high'];

for ($i = 0; $i < 100; $i++) {
    $slot = $slots[array_rand($slots)];
    $time = time();
    if (!$db->query("INSERT INTO jobs (target, slot, time_created, status) VALUES ('$target', '$slot', $time, 'waiting')"))
        echo "{$db->error}\n";
}

$db->close();