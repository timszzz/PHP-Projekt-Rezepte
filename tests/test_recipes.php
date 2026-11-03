<?php
// Prüft, ob findAll() alle Rezepte mit Kategorie liefert
require_once __DIR__ . '/../src/Db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/RecipeRepository.php';

$repo = new RecipeRepository(new Db());

foreach ($repo->findAll() as $recipe) {
    echo e($recipe['title']) . ' (' . e($recipe['category']) . ')<br>';
}