<?php
// Startseite: Liste aller Rezepte
require_once __DIR__ . '/src/Db.php';
require_once __DIR__ . '/src/helpers.php';
require_once __DIR__ . '/src/RecipeRepository.php';

// Repository erzeugen und Rezepte laden
$repo = new RecipeRepository(new Db());
$recipes = $repo->findAll();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Rezepte</title>
</head>
<body>
    <h1>Rezepte</h1>

    <ul>
        <?php foreach ($recipes as $recipe): ?>
            <li>
                <a href="recipe.php?id=<?= e($recipe['id']) ?>"><?= e($recipe['title']) ?></a>
                (<?= e($recipe['category']) ?>)
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>