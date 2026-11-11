<?php
// Alles rund um Login und Session

// Basispfad der Anwendung, damit Links aus jedem Unterordner funktionieren
const BASE_URL = '/php/PHP-Projekt-Rezepte';

// Startet die Session mit sicheren Einstellungen
// Muss vor der ersten HTML-Ausgabe laufen, weil dabei ein Cookie gesetzt wird
function startSession(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    // Lehnt Session-IDs ab, die der Server nicht selbst erzeugt hat
    ini_set('session.use_strict_mode', '1');

    session_set_cookie_params([
        'lifetime' => 0,                                 // Cookie endet mit dem Browser
        'path'     => '/',
        'httponly' => true,                              // JavaScript kommt nicht an das Cookie (XSS)
        'samesite' => 'Lax',                             // Kein Cookie bei POST von fremden Seiten (CSRF)
        'secure'   => ($_SERVER['HTTPS'] ?? 'off') !== 'off', // Bei HTTPS nur verschlüsselt senden
    ]);

    session_start();
}

// Prüft die Formularfelder und liefert eine Liste mit Fehlermeldungen
// mixed, weil zum Beispiel username[]=x im POST ein Array statt eines Strings ergibt
function validateLoginInput(mixed $username, mixed $password): array
{
    if (!is_string($username) || !is_string($password)) {
        return ['Die Formulardaten sind ungültig.'];
    }

    $errors = [];

    if (trim($username) === '') {
        $errors[] = 'Bitte geben Sie einen Benutzernamen ein.';
    }

    if ($password === '') {
        $errors[] = 'Bitte geben Sie ein Passwort ein.';
    }

    return $errors;
}

// Prüft die Daten für einen neuen Benutzer und liefert eine Liste mit Fehlermeldungen
function validateNewUser(mixed $username, mixed $password, mixed $passwordRepeat): array
{
    if (!is_string($username) || !is_string($password) || !is_string($passwordRepeat)) {
        return ['Die Formulardaten sind ungültig.'];
    }

    $errors = [];

    // Nur Buchstaben, Ziffern, Punkt, Unterstrich und Bindestrich, 3 bis 50 Zeichen
    if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
        $errors[] = 'Der Benutzername muss 3 bis 50 Zeichen lang sein und darf nur Buchstaben, Ziffern, Punkt, Unterstrich und Bindestrich enthalten.';
    }

    if (mb_strlen($password, 'UTF-8') < 8) {
        $errors[] = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
    } elseif (strlen($password) > 72) {
        // password_hash() mit bcrypt berücksichtigt nur die ersten 72 Bytes
        $errors[] = 'Das Passwort darf höchstens 72 Zeichen lang sein.';
    }

    if ($password !== $passwordRepeat) {
        $errors[] = 'Die beiden Passwörter stimmen nicht überein.';
    }

    return $errors;
}

// Liefert ein CSRF-Token für Formulare, pro Session einmal erzeugt
// Das Token kennt nur unsere eigene Seite, eine fremde Seite kann es nicht mitschicken
function csrfToken(): string
{
    startSession();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Prüft das mitgeschickte Token, hash_equals() vergleicht in konstanter Zeit
function checkCsrfToken(mixed $token): bool
{
    startSession();

    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

// Liefert den Benutzer, wenn Name und Passwort stimmen, sonst null
function checkCredentials(UserRepository $users, string $username, string $password): ?array
{
    $user = $users->findByUsername($username);

    if ($user === null || !password_verify($password, $user['password_hash'])) {
        return null;
    }

    return $user;
}

// Merkt sich den Benutzer in der Session
function loginUser(array $user): void
{
    startSession();

    // Neue Session-ID nach dem Login, die alte wird wertlos (Session Fixation)
    session_regenerate_id(true);

    // Nur ID und Name speichern, der Passwort-Hash bleibt in der Datenbank
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['username'] = $user['username'];
}

// Beendet die Session vollständig: Daten, Cookie und Datei auf dem Server
function logoutUser(): void
{
    startSession();

    $_SESSION = [];

    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 3600,
        'path'     => $params['path'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'],
        'secure'   => $params['secure'],
    ]);

    session_destroy();
}

function isLoggedIn(): bool
{
    startSession();

    return isset($_SESSION['user_id']);
}

// Name des angemeldeten Benutzers oder null
function currentUsername(): ?string
{
    startSession();

    return $_SESSION['username'] ?? null;
}

// Ganz oben in jede geschützte Seite einbauen, auch bei POST
// Wer nicht angemeldet ist, landet beim Login, der Rest der Seite läuft nicht mehr
function requireLogin(): void
{
    if (!isLoggedIn()) {
        // Einmalige Meldung für die Login-Seite, sie wird dort nach dem Anzeigen gelöscht
        $_SESSION['login_notice'] = 'Bitte melde dich an, um diese Seite zu nutzen.';
        header('Location: ' . BASE_URL . '/auth/login.php', true, 303);
        exit;
    }
}
