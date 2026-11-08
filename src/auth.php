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
        header('Location: ' . BASE_URL . '/login.php', true, 303);
        exit;
    }
}
