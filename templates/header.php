<?php
// Muss vor dem ersten HTML stehen, weil die Session ein Cookie setzt
require_once __DIR__ . '/../src/auth.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= e($pageTitle ?? 'Rezepte') ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav>
        <a href="<?= BASE_URL ?>/index.php">Rezepte</a>
        <?php if (isLoggedIn()): ?>
            <span>Angemeldet als <?= e(currentUsername()) ?></span>
            <!-- Formular statt Link: Abmelden ändert etwas und läuft deshalb per POST -->
            <form method="post" action="<?= BASE_URL ?>/logout.php" style="display:inline">
                <button type="submit">Abmelden</button>
            </form>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login.php">Anmelden</a>
        <?php endif; ?>
    </nav>