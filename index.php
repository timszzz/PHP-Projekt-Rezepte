<?php
// Startseite: Rezeptliste mit Suche
require_once __DIR__ . '/src/Db.php';
require_once __DIR__ . '/src/helpers.php';
require_once __DIR__ . '/src/RecipeRepository.php';

$repo = new RecipeRepository(new Db());

// Suchbegriff aus der URL lesen, fehlender oder ungültiger Wert wird zu ''
$term = trim((string) filter_input(INPUT_GET, 'q'));

// Ohne Suchbegriff alle Rezepte zeigen, sonst suchen
if ($term === '') {
    $recipes = $repo->findAll();
} else {
    $recipes = $repo->search($term);
}

$pageTitle = 'Rezepte';
require __DIR__ . '/templates/header.php';
?>
    <h1>Rezepte</h1>

    <form method="get" action="index.php">
        <label for="q">Suche</label>
        <input type="search" id="q" name="q" value="<?= e($term) ?>">
        <button type="submit">Suchen</button>
    </form>

    <?php if ($recipes): ?>
        <ul>
            <?php foreach ($recipes as $recipe): ?>
                <li>
                    <a href="recipe.php?id=<?= e($recipe['id']) ?>"><?= e($recipe['title']) ?></a>
                    (<?= e($recipe['category']) ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Keine Rezepte gefunden.</p>
    <?php endif; ?>
<?php require __DIR__ . '/templates/footer.php'; ?>