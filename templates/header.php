<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= e($pageTitle ?? 'Rezepte') ?></title>

    <!-- Gemeinsames Stylesheet für alle Seiten -->
    <!--<link rel="stylesheet" href="/php/PHP-Projekt-Rezepte/assets/style.css">-->

    <!-- Optionales zusätzliches Stylesheet für einzelne Seiten -->
    <?php if (!empty($extraCss)): ?>
        <link rel="stylesheet" href="<?= e($extraCss) ?>">
    <?php endif; ?>
</head>
<body>
   <nav>
      <a href="/php/PHP-Projekt-Rezepte/index.php">
         Rezepte
      </a>
   <br>
      <a href="/php/PHP-Projekt-Rezepte/recipes/create.php">
         Rezept erstellen
      </a>
   </nav>