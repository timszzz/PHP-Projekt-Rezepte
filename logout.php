<?php
// Logout: nur per POST, damit ein fremder Link niemanden einfach abmelden kann
require_once __DIR__ . '/src/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo 'Abmelden ist nur über den Button möglich.';
    exit;
}

logoutUser();

header('Location: index.php', true, 303);
exit;
