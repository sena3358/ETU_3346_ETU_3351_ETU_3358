<?php

function getDB() {
    $host = 'localhost';
    $dbname = 'tp_flight';
    $username = 'root';
<<<<<<< Updated upstream:ws/db.php
    $password = '3358';
=======
    $password = 'bolo1925';
>>>>>>> Stashed changes:tp-flightphp-crud/ws/db.php

    try {
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}
