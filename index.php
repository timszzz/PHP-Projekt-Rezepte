<?php
// Startseite: Rezeptliste mit Suche
require_once __DIR__ . '/src/Db.php';
require_once __DIR__ . '/src/helpers.php';
require_once __DIR__ . '/src/RecipeRepository.php';
require_once __DIR__ . '/src/CategoryRepository.php';

$repo = new RecipeRepository(new Db());
$categoryRepo = new CategoryRepository(new Db());

// Suchbegriff aus der URL lesen, fehlender oder ungültiger Wert wird zu ''
$term = trim((string) filter_input(INPUT_GET, 'q'));

// Ausgewählte Kategorie aus der URL lesen
$categoryId = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);

if ($categoryId === false || $categoryId <= 0) {
    $categoryId = null;
}

// Alle Kategorien laden
$categories = $categoryRepo->findAll();

// Kategorie gewählt: filtern, Suchbegriff: suchen, sonst alle Rezepte zeigen
if ($categoryId !== null) {
    $recipes = $repo->findByCategory($categoryId);
} elseif ($term !== '') {
    $recipes = $repo->search($term);
} else {
    $recipes = $repo->findAll();
}

$pageTitle = 'Rezepte';
require __DIR__ . '/templates/header.php';
?>
    <h1>Rezepte</h1>

<form method="get" action="index.php">
    <label for="category">Kategorie auswählen oder suchen</label>
    <select name="category" id="category">

        <option value="">Alle Kategorien</option>

        <?php foreach ($categories as $category): ?>
            <option
                value="<?= e($category['id']) ?>"
                <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>
            >
                <?= e($category['name']) ?>
            </option>
        <?php endforeach; ?>

    </select>

    <button type="submit">Filtern</button>

    </form>

    <form method="get" action="index.php">
        <label for="q">Suche</label>
        <input type="search" id="q" name="q" value="<?= e($term) ?>">
        <button type="submit">Suchen</button>
    </form>

    <?php if ($recipes): ?>
        <ul>
            <?php foreach ($recipes as $recipe): ?>
                <li>
                    <a href="<?= BASE_URL ?>/recipes/show.php?id=<?= e($recipe['id']) ?>"><?= e($recipe['title']) ?></a>
                    (<?= e($recipe['category']) ?>), von <?= e($recipe['author'] ?? 'Unbekannt') ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Keine Rezepte gefunden.</p>
    <?php endif; ?>
<?php require __DIR__ . '/templates/footer.php'; ?>