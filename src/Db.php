<?php

// Datenbankverbindung
class Db extends PDO
{
    // Konstruktor
    public function __construct()
    {
        // Zeigt der PDO (PHP Data Objects), wohin es sich verbinden soll
        $dsn = 'mysql:host=db;port=3306;dbname=rezepte;charset=utf8mb4';

        // Einstellungen, die die PDO beim Verbinden übernehmen soll
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // Ruft den Konstruktor der PDO auf
        parent::__construct($dsn, 'root', '', $options);
    }
}