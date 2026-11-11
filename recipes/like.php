
<?php

require_once __DIR__ . '/../src/Db.php';
require_once __DIR__ . '/../src/RecipeRepository.php';
require_once __DIR__ . '/../src/LikeRepository.php';
require_once __DIR__ . '/../src/auth.php';

// Nur angemeldete Benutzer dürfen liken
requireLogin();

// Nur POST-Anfragen erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Methode nicht erlaubt.');
}

// CSRF-Schutz
if (!checkCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    exit('Ungültiges CSRF-Token.');
}

// Rezept-ID prüfen
$recipeId = filter_input(
    INPUT_POST,
    'recipe_id',
    FILTER_VALIDATE_INT
);

if (!$recipeId || $recipeId < 1) {
    http_response_code(400);
    exit('Ungültige Rezept-ID.');
}

$db = new Db();

$recipeRepo = new RecipeRepository($db);
$recipe = $recipeRepo->findById($recipeId);

// Nur existierende Rezepte liken
if ($recipe === null) {
    http_response_code(404);
    exit('Rezept nicht gefunden.');
}

// Benutzer-ID aus der Session
$userId = (int) $_SESSION['user_id'];

$likeRepo = new LikeRepository($db);
$likeRepo->toggle($recipeId, $userId);

// Zurück zur Rezeptseite
header(
    'Location: ' . BASE_URL . '/recipes/show.php?id=' . $recipeId,
    true,
    303
);
exit;
