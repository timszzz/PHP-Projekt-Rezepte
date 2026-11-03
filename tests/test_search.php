<?php
// Prüft die Suche mit Treffer, ohne Treffer und mit einem Injection-Versuch
require_once __DIR__ . '/../src/Db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/RecipeRepository.php';

$repo = new RecipeRepository(new Db());

$terms = ['pfann', 'gibtsnicht', "' OR 1=1 -- "];

foreach ($terms as $term) {
    $results = $repo->search($term);
    echo e($term) . ': ' . count($results) . ' Treffer<br>';
}