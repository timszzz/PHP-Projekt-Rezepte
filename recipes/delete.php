<?php
// Rezept löschen: nur der Ersteller darf löschen.
require_once __DIR__ . '/../src/Db.php';
require_once __DIR__ . '/../src/RecipeRepository.php';
require_once __DIR__ . '/../src/auth.php';

requireLogin();

// Löschen darf ausschließlich über ein POST-Formular erfolgen.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Diese Aktion ist nur über das Löschformular möglich.');
}

// CSRF-Token prüfen, bevor eine Änderung an der Datenbank erfolgt.
if (!checkCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    exit('Das Formular ist abgelaufen oder ungültig.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(404);
    exit('Rezept nicht gefunden.');
}

$recipeRepo = new RecipeRepository(new Db());
$userId = (int) $_SESSION['user_id'];

// SQL löscht nur dann, wenn Rezept-ID UND angemeldete Benutzer-ID passen.
$recipeRepo->deleteOwnedRecipes($id, $userId);

// Nach dem Löschen zurück zur Übersicht (auch wenn das Rezept nicht dem User gehörte).
header('Location: ' . BASE_URL . '/index.php', true, 303);
exit;
