<?php
// Detailseite: zeigt ein einzelnes Rezept
require_once __DIR__ . '/src/Db.php';
require_once __DIR__ . '/src/helpers.php';
require_once __DIR__ . '/src/RecipeRepository.php';

// ID aus der URL prüfen: Zahl bei gültigem Wert, sonst false oder null
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$recipe = null;

if ($id) {
    $repo = new RecipeRepository(new Db());
    $recipe = $repo->findById($id);
}

if ($recipe === null) {
    http_response_code(404);
}

$pageTitle = $recipe ? $recipe['title'] : 'Rezept nicht gefunden';
require __DIR__ . '/templates/header.php';
?>
    <?php if ($recipe): ?>
        <h1><?= e($recipe['title']) ?></h1>
        <p>Kategorie: <?= e($recipe['category']) ?></p>

        <h2>Zutaten</h2>
        <p><?= nl2br(e($recipe['ingredients'])) ?></p>

        <h2>Zubereitung</h2>
        <p><?= nl2br(e($recipe['preparation'])) ?></p>
    <?php else: ?>
        <h1>Rezept nicht gefunden</h1>
        <p>Dieses Rezept gibt es nicht.</p>
    <?php endif; ?>
<?php require __DIR__ . '/templates/footer.php'; ?>