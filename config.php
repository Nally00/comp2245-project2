<?php
    try {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=bugme;charset=utf8mb4",
            "root",         // Please replace with your MySQL username
            "",             // Please replace with your MySQL password
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    } catch (PDOException $e) {
        error_log($e->getMessage());
        exit('Unable to connect to the database');
    }

?>
