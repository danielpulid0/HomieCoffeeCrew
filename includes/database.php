<?php

mysqli_report(MYSQLI_REPORT_OFF);

$db = mysqli_connect('localhost', 'root', '', 'homie_coffee_crew');

if (!$db) {
    $db = false;
} else {
    mysqli_set_charset($db, 'utf8mb4');
}