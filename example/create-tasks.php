<?php

// this just adds a bunch of meaningless tasks, for testing purposees
//
// in a real world, you will have additional fields in your table
// like 'job_name' and 'job_data'

$db = new mysqli();
if (!$db->real_connect('10.211.55.6', 'jobd', 'password', 'jobd'))
    die('Failed to connect.');

$target = 'server3';
$slots = ['low', 'normal', 'high'];

for ($i = 0; $i < 100; $i++) {
    $slot = $slots[array_rand($slots)];
    $time = time();
    if (!$db->query("INSERT INTO jobs (target, slot, time_created, status) VALUES ('$target', '$slot', $time, 'waiting')"))
        echo "{$db->error}\n";
}

$db->close();