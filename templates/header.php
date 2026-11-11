<?php
// Muss vor dem ersten HTML stehen, weil die Session ein Cookie setzt
require_once __DIR__ . '/../src/auth.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= e($pageTitle ?? 'Rezepte') ?></title>

    <!-- Gemeinsames Stylesheet für alle Seiten -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">

    <!-- Optionales zusätzliches Stylesheet für einzelne Seiten -->
    <?php if (!empty($extraCss)): ?>
        <link rel="stylesheet" href="<?= e($extraCss) ?>">
    <?php endif; ?>
</head>
<body>
    <nav>
        <a href="<?= BASE_URL ?>/index.php">Rezepte</a>
        <?php if (isLoggedIn()): ?>
            <a href="<?= BASE_URL ?>/recipes/create.php">Rezept erstellen</a>
            <a href="<?= BASE_URL ?>/users/manage.php">Benutzer</a>
            <span>Angemeldet als <?= e(currentUsername()) ?></span>
            <!-- Formular statt Link: Abmelden ändert etwas und läuft deshalb per POST -->
            <form method="post" action="<?= BASE_URL ?>/auth/logout.php" style="display:inline">
                <button type="submit">Abmelden</button>
            </form>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/auth/login.php">Anmelden</a>
        <?php endif; ?>
    </nav>
