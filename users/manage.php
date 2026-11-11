<?php
// Benutzerverwaltung: zeigt alle Benutzer, angemeldete Benutzer können neue anlegen
require_once __DIR__ . '/../src/Db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/UserRepository.php';
require_once __DIR__ . '/../src/auth.php';

// Nur für angemeldete Benutzer, gilt auch für das Absenden des Formulars
requireLogin();

$users = new UserRepository(new Db());
$errors = [];
$username = '';

// Einmalige Erfolgsmeldung aus der Session lesen und löschen
$success = $_SESSION['user_success'] ?? '';
unset($_SESSION['user_success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrfToken($_POST['csrf_token'] ?? null)) {
        // Ohne gültiges Token wird nichts gespeichert, egal was sonst im Formular steht
        http_response_code(400);
        $errors[] = 'Das Formular ist abgelaufen. Bitte versuchen Sie es erneut.';
    } else {
        $usernameInput = $_POST['username'] ?? '';
        $passwordInput = $_POST['password'] ?? '';
        $repeatInput = $_POST['password_repeat'] ?? '';

        $errors = validateNewUser($usernameInput, $passwordInput, $repeatInput);

        if (is_string($usernameInput)) {
            $username = trim($usernameInput);
        }

        if (!$errors && $users->findByUsername($username) !== null) {
            $errors[] = 'Diesen Benutzernamen gibt es schon.';
        }

        if (!$errors) {
            // Nur der Hash kommt in die Datenbank, nie das Passwort selbst
            $users->create($username, password_hash($passwordInput, PASSWORD_DEFAULT));

            $_SESSION['user_success'] = 'Der Benutzer ' . $username . ' wurde angelegt.';

            // PRG: Neu laden schickt das Formular nicht noch einmal ab
            header('Location: ' . BASE_URL . '/users/manage.php', true, 303);
            exit;
        }
    }
}

$allUsers = $users->findAll();

$pageTitle = 'Benutzer';
require __DIR__ . '/../templates/header.php';
?>
    <h1>Benutzer</h1>

    <?php if ($success !== ''): ?>
        <p><?= e($success) ?></p>
    <?php endif; ?>

    <ul>
        <?php foreach ($allUsers as $user): ?>
            <li><?= e($user['username']) ?> (seit <?= e(date('d.m.Y', strtotime($user['created_at']))) ?>)</li>
        <?php endforeach; ?>
    </ul>

    <h2>Neuen Benutzer anlegen</h2>

    <?php foreach ($errors as $error): ?>
        <p role="alert"><?= e($error) ?></p>
    <?php endforeach; ?>

    <form method="post" action="<?= BASE_URL ?>/users/manage.php">
        <!-- Unsichtbares Feld mit dem CSRF-Token, wird beim Absenden geprüft -->
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <p>
            <label for="username">Benutzername</label><br>
            <input type="text" id="username" name="username" maxlength="50"
                   autocomplete="off" value="<?= e($username) ?>" required>
        </p>
        <p>
            <label for="password">Passwort (mindestens 8 Zeichen)</label><br>
            <input type="password" id="password" name="password" minlength="8"
                   autocomplete="new-password" required>
        </p>
        <p>
            <label for="password_repeat">Passwort wiederholen</label><br>
            <input type="password" id="password_repeat" name="password_repeat" minlength="8"
                   autocomplete="new-password" required>
        </p>
        <button type="submit">Benutzer anlegen</button>
    </form>
<?php require __DIR__ . '/../templates/footer.php'; ?>
