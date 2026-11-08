<?php
// Prüft requireLogin() im Browser
// Ohne Login leitet die Seite zum Formular weiter, mit Login erscheint der Text
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/auth.php';

requireLogin();

echo 'Geschützter Bereich, angemeldet als ' . e(currentUsername());
