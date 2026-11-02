<?php
// Prüft, ob die Datenbankverbindung steht und die Testdaten importiert sind
require_once __DIR__ . '/../src/Db.php';

try {
    $db = new Db();
    $stmt = $db->query('SELECT COUNT(*) FROM main_recipe');
    echo 'Verbindung OK. Anzahl Rezepte: ' . $stmt->fetchColumn();
} catch (PDOException $ex) {
    echo 'Fehler: ' . $ex->getMessage();
}