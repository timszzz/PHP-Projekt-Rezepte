<?php
// Prüft Validierung, Passwortprüfung und das Anlegen von Benutzern
// Erwartet die Testdaten aus database/seed.sql (admin / test123)
require_once __DIR__ . '/../src/Db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/UserRepository.php';
require_once __DIR__ . '/../src/auth.php';

function check(string $name, bool $passed): void
{
    echo($passed ? 'OK: ' : 'FEHLER: ') . e($name) . '<br>';
}

$db = new Db();
$users = new UserRepository($db);

// Validierung
check('Gültige Eingabe ergibt keinen Fehler', validateLoginInput('admin', 'test123') === []);
check('Leerer Benutzername wird abgelehnt', count(validateLoginInput('   ', 'test123')) === 1);
check('Leeres Passwort wird abgelehnt', count(validateLoginInput('admin', '')) === 1);
check('Array statt String wird abgelehnt', count(validateLoginInput(['admin'], 'test123')) === 1);

// Anmeldedaten
check('Richtiges Passwort wird akzeptiert', checkCredentials($users, 'admin', 'test123') !== null);
check('Falsches Passwort wird abgelehnt', checkCredentials($users, 'admin', 'falsch') === null);
check('Unbekannter Benutzer wird abgelehnt', checkCredentials($users, 'gibtsnicht', 'test123') === null);
check('SQL Injection findet keinen Benutzer', $users->findByUsername("' OR 1=1 -- ") === null);

// Validierung für neue Benutzer
check('Gültiger neuer Benutzer ergibt keinen Fehler', validateNewUser('gleb', 'geheim123', 'geheim123') === []);
check('Zu kurzer Benutzername wird abgelehnt', count(validateNewUser('ab', 'geheim123', 'geheim123')) === 1);
check('Leerzeichen und HTML im Namen werden abgelehnt', count(validateNewUser('<b>x</b> y', 'geheim123', 'geheim123')) === 1);
check('Zu kurzes Passwort wird abgelehnt', count(validateNewUser('gleb', 'kurz', 'kurz')) === 1);
check('Zu langes Passwort wird abgelehnt', count(validateNewUser('gleb', str_repeat('a', 73), str_repeat('a', 73))) === 1);
check('Unterschiedliche Passwörter werden abgelehnt', count(validateNewUser('gleb', 'geheim123', 'geheim124')) === 1);
check('Array statt String wird abgelehnt', count(validateNewUser(['gleb'], 'geheim123', 'geheim123')) === 1);
check('Benutzerliste enthält keinen Passwort-Hash', !array_key_exists('password_hash', $users->findAll()[0]));

// Neuen Benutzer anlegen und am Ende wieder entfernen, damit die Testdaten gleich bleiben
$testName = 'testuser_' . bin2hex(random_bytes(4));
$password = 'geheim123';

try {
    $id = $users->create($testName, password_hash($password, PASSWORD_DEFAULT));
    $created = $users->findByUsername($testName);

    check('Neuer Benutzer bekommt eine ID', $id > 0);
    check('Passwort liegt nicht im Klartext in der Datenbank', $created['password_hash'] !== $password);
    check('Neuer Benutzer kann sich anmelden', checkCredentials($users, $testName, $password) !== null);
} finally {
    $stmt = $db->prepare('DELETE FROM main_user WHERE username = :username');
    $stmt->execute(['username' => $testName]);
}
