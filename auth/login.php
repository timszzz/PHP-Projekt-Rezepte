<?php
// Login-Seite: GET zeigt das Formular, POST prüft die Anmeldedaten
require_once __DIR__ . '/../src/Db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/UserRepository.php';
require_once __DIR__ . '/../src/auth.php';

// Wer schon angemeldet ist, braucht das Formular nicht
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/index.php', true, 303);
    exit;
}

$errors = [];
$username = '';

// Hinweis von requireLogin() lesen und gleich löschen, damit er nur einmal erscheint
$notice = $_SESSION['login_notice'] ?? '';
unset($_SESSION['login_notice']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameInput = $_POST['username'] ?? '';
    $passwordInput = $_POST['password'] ?? '';

    $errors = validateLoginInput($usernameInput, $passwordInput);

    if (!$errors) {
        $username = trim($usernameInput);
        $user = checkCredentials(new UserRepository(new Db()), $username, $passwordInput);

        if ($user !== null) {
            loginUser($user);

            // Weiterleiten, damit ein Neuladen das Formular nicht erneut abschickt (PRG)
            header('Location: ' . BASE_URL . '/index.php', true, 303);
            exit;
        }

        // Gleiche Meldung für beide Fälle: So verrät der Login nicht, welche Benutzernamen es gibt
        $errors[] = 'Benutzername oder Passwort ist falsch.';
    }
}

$pageTitle = 'Anmelden';
require __DIR__ . '/../templates/header.php';
?>
    <h1>Anmelden</h1>

    <?php if ($notice !== ''): ?>
        <p><?= e($notice) ?></p>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <p role="alert"><?= e($error) ?></p>
    <?php endforeach; ?>

    <form method="post" action="<?= BASE_URL ?>/auth/login.php">
        <p>
            <label for="username">Benutzername</label><br>
            <input type="text" id="username" name="username" maxlength="50"
                   autocomplete="username" value="<?= e($username) ?>" required>
        </p>
        <p>
            <label for="password">Passwort</label><br>
            <!-- Kein value: Das Passwort wird nie zurück ins Formular geschrieben -->
            <input type="password" id="password" name="password"
                   autocomplete="current-password" required>
        </p>
        <button type="submit">Anmelden</button>
    </form>
<?php require __DIR__ . '/../templates/footer.php'; ?>
