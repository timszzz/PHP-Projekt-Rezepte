<?php
// Prüft, ob e() HTML-Sonderzeichen maskiert
require_once __DIR__ . '/../src/helpers.php';

echo e("<script>alert('x')</script>");
echo '<br>';
echo 'Leerer Wert: [' . e(null) . ']';